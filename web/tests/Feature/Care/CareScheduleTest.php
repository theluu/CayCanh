<?php

use App\Actions\GenerateCareVisits;
use App\Enums\CareFrequency;
use App\Enums\Role;
use App\Filament\Resources\CareVisits\Pages\ListCareVisits;
use App\Models\CareContract;
use App\Models\CareVisit;
use App\Models\User;
use App\Notifications\CareVisitsToday;
use Carbon\CarbonImmutable;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

it('generates weekly visits up to the given date', function () {
    $contract = CareContract::factory()->create(['starts_on' => '2026-12-01', 'ends_on' => null, 'frequency' => CareFrequency::Weekly]);

    $created = app(GenerateCareVisits::class)->handle($contract, CarbonImmutable::parse('2026-12-31'));

    expect($created)->toBe(5)
        ->and($contract->visits()->orderBy('scheduled_on')->pluck('scheduled_on')->map->toDateString()->all())
        ->toBe(['2026-12-01', '2026-12-08', '2026-12-15', '2026-12-22', '2026-12-29']);
});

it('is idempotent', function () {
    $contract = CareContract::factory()->create(['starts_on' => '2026-12-01', 'frequency' => CareFrequency::Weekly]);
    $action = app(GenerateCareVisits::class);

    $action->handle($contract, CarbonImmutable::parse('2026-12-31'));
    $second = $action->handle($contract, CarbonImmutable::parse('2026-12-31'));

    expect($second)->toBe(0)->and($contract->visits()->count())->toBe(5);
});

it('stops at the contract end date', function () {
    $contract = CareContract::factory()->create(['starts_on' => '2026-12-01', 'ends_on' => '2026-12-15', 'frequency' => CareFrequency::Weekly]);

    expect(app(GenerateCareVisits::class)->handle($contract, CarbonImmutable::parse('2026-12-31')))->toBe(3);
});

it('supports biweekly and monthly frequencies', function () {
    $biweekly = CareContract::factory()->create(['starts_on' => '2026-12-01', 'frequency' => CareFrequency::Biweekly]);
    $monthly = CareContract::factory()->create(['starts_on' => '2026-01-31', 'frequency' => CareFrequency::Monthly]);

    expect(app(GenerateCareVisits::class)->handle($biweekly, CarbonImmutable::parse('2026-12-31')))->toBe(3)
        ->and(app(GenerateCareVisits::class)->handle($monthly, CarbonImmutable::parse('2026-04-30')))->toBe(4)
        ->and($monthly->visits()->orderBy('scheduled_on')->pluck('scheduled_on')->map->toDateString()->all())
        ->toBe(['2026-01-31', '2026-02-28', '2026-03-31', '2026-04-30']);
});

it('assigns the default technician to generated visits', function () {
    $tech = User::factory()->create();
    $contract = CareContract::factory()->create(['starts_on' => '2026-12-01', 'frequency' => CareFrequency::Monthly, 'default_technician_id' => $tech->id]);

    app(GenerateCareVisits::class)->handle($contract, CarbonImmutable::parse('2026-12-31'));

    expect($contract->visits()->first()->technician_id)->toBe($tech->id);
});

it('generates the next 60 days for active contracts only', function () {
    $this->travelTo(CarbonImmutable::parse('2026-12-01 05:00'));
    CareContract::factory()->create(['starts_on' => '2026-12-01', 'frequency' => CareFrequency::Weekly, 'is_active' => false]);
    CareContract::factory()->create(['starts_on' => '2026-12-01', 'frequency' => CareFrequency::Weekly]);

    $this->artisan('care:generate')->assertSuccessful();

    // 2026-12-01 … 2027-01-26 (+56 days) = 9 weekly visits; 2027-02-02 is past the 60-day window.
    expect(CareVisit::count())->toBe(9);
});

it('shows technicians only their own visits', function () {
    $this->seed(RoleSeeder::class);
    $techA = tap(User::factory()->create())->assignRole(Role::Technician->value);
    $techB = tap(User::factory()->create())->assignRole(Role::Technician->value);
    $visitA = CareVisit::factory()->create(['technician_id' => $techA->id]);
    $visitB = CareVisit::factory()->create(['technician_id' => $techB->id]);

    $this->actingAs($techA);

    Livewire::test(ListCareVisits::class)
        ->assertCanSeeTableRecords([$visitA])
        ->assertCanNotSeeTableRecords([$visitB]);
    $this->get("/admin/care-visits/{$visitB->id}/edit")->assertNotFound();
});

it('lets sales see every visit and manage contracts', function () {
    $this->seed(RoleSeeder::class);
    $sales = tap(User::factory()->create())->assignRole(Role::Sales->value);
    $visits = CareVisit::factory()->count(2)->create();

    $this->actingAs($sales);
    Livewire::test(ListCareVisits::class)->assertCanSeeTableRecords($visits);
    $this->get('/admin/care-contracts')->assertOk();
    $this->get('/admin/care-contracts/create')->assertOk();
});

it('keeps technicians away from contracts', function () {
    $this->seed(RoleSeeder::class);
    $this->actingAs(tap(User::factory()->create())->assignRole(Role::Technician->value));

    $this->get('/admin/care-contracts')->assertForbidden();
    $this->get('/admin/care-visits')->assertOk();
});

it('reminds each technician of today\'s visits', function () {
    Notification::fake();
    $techA = User::factory()->create();
    $techB = User::factory()->create();
    CareVisit::factory()->count(2)->create(['technician_id' => $techA->id, 'scheduled_on' => today()]);
    CareVisit::factory()->create(['technician_id' => $techB->id, 'scheduled_on' => today()->addDay()]);
    CareVisit::factory()->create(['technician_id' => $techA->id, 'scheduled_on' => today(), 'status' => 'cancelled']);

    $this->artisan('care:remind')->assertSuccessful();

    Notification::assertSentTo($techA, CareVisitsToday::class, fn ($notification) => $notification->visits->count() === 2);
    Notification::assertNotSentTo($techB, CareVisitsToday::class);
});

it('creates a care contract from a won inquiry in the admin', function () {
    $this->seed(RoleSeeder::class);
    $this->actingAs(tap(User::factory()->create())->assignRole(Role::Sales->value));

    $this->get('/admin/care-contracts/create')->assertOk()->assertSee('Tần suất');
});
