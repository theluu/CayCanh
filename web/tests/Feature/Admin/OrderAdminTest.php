<?php

use App\Enums\OrderStatus;
use App\Enums\Role;
use App\Filament\Resources\Inquiries\InquiryResource;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Widgets\NewLeadsStats;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

function adminUser(Role $role): User
{
    return tap(User::factory()->create())->assignRole($role->value);
}

it('lets sales and admins manage orders and inquiries', function (Role $role) {
    $order = Order::factory()->create();
    $order->items()->create(['product_name' => 'Cây Kim Tiền', 'unit_price' => 100000, 'qty' => 2, 'line_total' => 200000]);
    $inquiry = Inquiry::factory()->create();

    $this->actingAs(adminUser($role));
    $this->get('/admin/orders')->assertOk();
    $this->get("/admin/orders/{$order->id}/edit")->assertOk()->assertSee('Cây Kim Tiền');
    $this->get('/admin/inquiries')->assertOk();
    $this->get("/admin/inquiries/{$inquiry->id}/edit")->assertOk();
})->with([Role::Sales, Role::Admin]);

it('opens an inquiry without a subject without querying an empty morph column', function () {
    $inquiry = Inquiry::factory()->create(['type' => 'survey', 'subject_type' => null, 'subject_id' => null]);
    $queries = [];
    DB::listen(function ($query) use (&$queries) {
        $queries[] = $query->sql;
    });

    $this->actingAs(adminUser(Role::Sales))->get("/admin/inquiries/{$inquiry->id}/edit")->assertOk();

    expect(collect($queries)->filter(fn (string $sql) => str_contains($sql, 'where "" is null') || str_contains($sql, 'where `` is null')))->toBeEmpty();
});

it('keeps editors and technicians out of orders and inquiries', function (Role $role) {
    $this->actingAs(adminUser($role));

    $this->get('/admin/orders')->assertForbidden();
    $this->get('/admin/inquiries')->assertForbidden();
})->with([Role::Editor, Role::Technician]);

it('updates the order status and internal note', function () {
    $this->actingAs(adminUser(Role::Sales));
    $order = Order::factory()->create();

    Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])
        ->fillForm(['status' => OrderStatus::Confirmed->value, 'admin_note' => 'Khách hẹn chiều mai'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($order->fresh()->status)->toBe(OrderStatus::Confirmed)
        ->and($order->fresh()->admin_note)->toBe('Khách hẹn chiều mai');
});

it('counts new leads on the dashboard', function () {
    $this->actingAs(adminUser(Role::Sales));
    Inquiry::factory()->count(3)->create();
    Inquiry::factory()->create(['status' => 'won']);
    Order::factory()->count(2)->create();

    Livewire::test(NewLeadsStats::class)
        ->assertSeeText('Yêu cầu mới')
        ->assertSeeText('3')
        ->assertSeeText('Đơn hàng mới')
        ->assertSeeText('2');
});

it('shows a badge with the number of new inquiries in the navigation', function () {
    Inquiry::factory()->count(4)->create();

    expect(InquiryResource::getNavigationBadge())->toBe('4');
});
