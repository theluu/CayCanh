<?php

use App\Actions\MarkArtSold;
use App\Actions\ReleaseArtTree;
use App\Actions\ReserveArtTree;
use App\Enums\ArtAvailability;
use App\Enums\InquiryStatus;
use App\Enums\Role;
use App\Exceptions\ArtNotAvailable;
use App\Filament\Resources\Inquiries\Pages\EditInquiry;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Livewire\Livewire;

function artInquiry(Product $art): Inquiry
{
    return Inquiry::factory()->create(['type' => 'quote_product', 'subject_type' => 'product', 'subject_id' => $art->id]);
}

it('reserves an available art tree for an inquiry', function () {
    $art = Product::factory()->art()->create();
    $inquiry = artInquiry($art);

    app(ReserveArtTree::class)->handle($art, $inquiry);

    expect($art->fresh()->availability)->toBe(ArtAvailability::Reserved)
        ->and($art->fresh()->reserved_inquiry_id)->toBe($inquiry->id)
        ->and($inquiry->fresh()->status)->toBe(InquiryStatus::Processing);
});

it('refuses to reserve a tree that is already held or sold', function (ArtAvailability $state) {
    $art = Product::factory()->art()->create(['availability' => $state]);

    app(ReserveArtTree::class)->handle($art, artInquiry($art));
})->with([ArtAvailability::Reserved, ArtAvailability::Sold])->throws(ArtNotAvailable::class);

it('refuses to reserve a standard product', function () {
    $product = Product::factory()->create();

    app(ReserveArtTree::class)->handle($product, artInquiry($product));
})->throws(ArtNotAvailable::class);

it('marks a tree as sold and releases a held tree', function () {
    $art = Product::factory()->art()->create();
    app(ReserveArtTree::class)->handle($art, artInquiry($art));

    app(ReleaseArtTree::class)->handle($art);
    expect($art->fresh()->availability)->toBe(ArtAvailability::Available)
        ->and($art->fresh()->reserved_inquiry_id)->toBeNull();

    app(MarkArtSold::class)->handle($art);
    expect($art->fresh()->availability)->toBe(ArtAvailability::Sold);
});

it('lets sales reserve the tree from the inquiry page', function () {
    $this->seed(RoleSeeder::class);
    $this->actingAs(tap(User::factory()->create())->assignRole(Role::Sales->value));
    $art = Product::factory()->art()->create();
    $inquiry = artInquiry($art);

    Livewire::test(EditInquiry::class, ['record' => $inquiry->getRouteKey()])
        ->callAction('reserveTree')
        ->assertNotified();

    expect($art->fresh()->reserved_inquiry_id)->toBe($inquiry->id);
});

it('offers to release the tree after the holding inquiry is lost', function () {
    $this->seed(RoleSeeder::class);
    $this->actingAs(tap(User::factory()->create())->assignRole(Role::Sales->value));
    $art = Product::factory()->art()->create();
    $inquiry = artInquiry($art);
    app(ReserveArtTree::class)->handle($art, $inquiry);

    Livewire::test(EditInquiry::class, ['record' => $inquiry->getRouteKey()])
        ->fillForm(['status' => InquiryStatus::Lost->value])
        ->call('save')
        ->assertNotified('Yêu cầu đang giữ cây '.$art->name)
        ->assertActionVisible('releaseTree')
        ->callAction('releaseTree');

    expect($art->fresh()->availability)->toBe(ArtAvailability::Available);
});
