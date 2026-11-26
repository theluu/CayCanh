<?php

use App\Enums\InquiryStatus;
use App\Enums\InquiryType;
use App\Events\InquirySubmitted;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServicePackage;
use Illuminate\Support\Facades\Event;

function inquiryData(array $overrides = []): array
{
    return array_merge([
        'type' => 'contact',
        'name' => 'Trần Thị B',
        'phone' => '0912 345 678',
        'email' => 'b@example.com',
        'message' => 'Tôi cần tư vấn cây văn phòng.',
        'website' => '',
    ], $overrides);
}

it('stores a contact inquiry with a normalized phone and fires an event', function () {
    Event::fake([InquirySubmitted::class]);

    $this->from('/lien-he')->post('/lien-he', inquiryData())
        ->assertRedirect('/lien-he')
        ->assertSessionHas('success');

    $inquiry = Inquiry::sole();
    expect($inquiry->type)->toBe(InquiryType::Contact)
        ->and($inquiry->status)->toBe(InquiryStatus::New)
        ->and($inquiry->phone)->toBe('0912345678');
    Event::assertDispatched(InquirySubmitted::class);
});

it('links a product quote to the art tree', function () {
    $art = Product::factory()->art()->create();

    $this->post('/lien-he', inquiryData(['type' => 'quote_product', 'subject_type' => 'product', 'subject_id' => $art->id]));

    expect(Inquiry::sole()->subject->is($art))->toBeTrue();
});

it('links service and package quotes', function () {
    $package = ServicePackage::factory()->create();

    $this->post('/lien-he', inquiryData(['type' => 'quote_service', 'subject_type' => 'package', 'subject_id' => $package->id]));
    $this->post('/lien-he', inquiryData(['type' => 'quote_service', 'subject_type' => 'service', 'subject_id' => $package->service_id]));

    expect(Inquiry::first()->subject->is($package))->toBeTrue()
        ->and(Inquiry::latest('id')->first()->subject)->toBeInstanceOf(Service::class);
});

it('stores survey details in meta', function () {
    $this->post('/lien-he', inquiryData([
        'type' => 'survey',
        'meta' => ['project_kind' => 'Văn phòng', 'location' => 'Quận 7', 'area' => '500', 'budget' => '100 triệu', 'timeline' => 'Tháng 1'],
    ]));

    expect(Inquiry::sole()->meta)->toMatchArray(['project_kind' => 'Văn phòng', 'area' => '500']);
});

it('rejects an invalid phone or missing name', function () {
    $this->post('/lien-he', inquiryData(['phone' => '12345', 'name' => '']))->assertSessionHasErrors(['phone', 'name']);

    expect(Inquiry::count())->toBe(0);
});

it('rejects a subject that does not exist', function () {
    $this->post('/lien-he', inquiryData(['type' => 'quote_product', 'subject_type' => 'product', 'subject_id' => 999]))
        ->assertSessionHasErrors('subject_id');
});

it('silently drops honeypot submissions', function () {
    Event::fake([InquirySubmitted::class]);

    $this->post('/lien-he', inquiryData(['website' => 'http://spam.example']))->assertRedirect()->assertSessionHas('success');

    expect(Inquiry::count())->toBe(0);
    Event::assertNotDispatched(InquirySubmitted::class);
});

it('throttles more than five submissions per minute from one ip', function () {
    foreach (range(1, 5) as $i) {
        $this->post('/lien-he', inquiryData())->assertRedirect();
    }

    $this->post('/lien-he', inquiryData())->assertStatus(429);
    expect(Inquiry::count())->toBe(5);
});

it('renders the survey quote page', function () {
    $this->get('/bao-gia')->assertOk()->assertInertia(fn ($page) => $page->component('Quote'));
});
