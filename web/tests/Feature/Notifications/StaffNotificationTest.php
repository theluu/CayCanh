<?php

use App\Enums\ArtAvailability;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\Product;
use App\Notifications\InquiryReceivedCustomer;
use App\Notifications\NewInquiryNotification;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderReceivedCustomer;
use App\Settings\GeneralSettings;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;

function configureStaffChannels(?string $chatId = '-100200300', array $emails = ['sales@example.com']): void
{
    config(['services.telegram-bot-api.token' => 'test-token']);
    $settings = app(GeneralSettings::class);
    $settings->telegram_chat_id = $chatId;
    $settings->notify_emails = $emails;
    $settings->save();
}

function inquiryPayload(array $overrides = []): array
{
    return array_merge(['type' => 'contact', 'name' => 'Lê C', 'phone' => '0987654321', 'email' => 'c@example.com', 'message' => 'Cần tư vấn'], $overrides);
}

it('notifies staff on telegram and email when an inquiry arrives', function () {
    Notification::fake();
    configureStaffChannels();

    $this->post('/lien-he', inquiryPayload());

    Notification::assertSentOnDemand(NewInquiryNotification::class, function ($notification, array $channels, AnonymousNotifiable $notifiable) {
        return in_array('telegram', $channels, true)
            && in_array('mail', $channels, true)
            && $notifiable->routes['telegram'] === '-100200300'
            && $notifiable->routes['mail'] === ['sales@example.com'];
    });
    Notification::assertSentOnDemand(InquiryReceivedCustomer::class);
});

it('skips telegram when no chat id is configured', function () {
    Notification::fake();
    configureStaffChannels(chatId: null);

    $this->post('/lien-he', inquiryPayload());

    Notification::assertSentOnDemand(NewInquiryNotification::class, fn ($n, array $channels) => $channels === ['mail']);
});

it('does not email the customer when no email was given', function () {
    Notification::fake();
    configureStaffChannels();

    $this->post('/lien-he', inquiryPayload(['email' => '']));

    Notification::assertNotSentTo(new AnonymousNotifiable, InquiryReceivedCustomer::class);
    Notification::assertSentOnDemandTimes(InquiryReceivedCustomer::class, 0);
});

it('notifies staff and the customer about a new order', function () {
    Notification::fake();
    configureStaffChannels();
    $product = Product::factory()->create();
    $this->post('/gio-hang', ['product_id' => $product->id, 'qty' => 1]);

    $this->post('/dat-hang', [
        'customer_name' => 'Phạm D', 'phone' => '0901234567', 'email' => 'd@example.com',
        'province' => 'Hà Nội', 'address' => '1 Tràng Tiền', 'payment_method' => 'cod',
    ]);

    Notification::assertSentOnDemand(NewOrderNotification::class, fn ($notification) => $notification->order->is(Order::sole()));
    Notification::assertSentOnDemand(OrderReceivedCustomer::class);
});

it('still saves the inquiry when telegram is unreachable', function () {
    configureStaffChannels();
    config(['services.telegram-bot-api.base_uri' => 'http://127.0.0.1:9']);

    $this->from('/lien-he')->post('/lien-he', inquiryPayload())
        ->assertRedirect('/lien-he')
        ->assertSessionHas('success');

    expect(Inquiry::count())->toBe(1);
});

it('warns staff when the requested art tree is already reserved', function () {
    $art = Product::factory()->art()->create(['availability' => ArtAvailability::Reserved, 'reserved_inquiry_id' => 42]);
    $inquiry = Inquiry::factory()->create(['type' => 'quote_product', 'subject_type' => 'product', 'subject_id' => $art->id]);

    $text = (new NewInquiryNotification($inquiry))->toTelegram(new AnonymousNotifiable)->toArray()['text'];

    expect($text)->toContain('Cây đang giữ cho yêu cầu #42')
        ->and($text)->toContain($art->name);
});

it('links the staff message to the admin record', function () {
    $inquiry = Inquiry::factory()->create();

    $mail = (new NewInquiryNotification($inquiry))->toMail(new AnonymousNotifiable);

    expect($mail->actionUrl)->toBe(url('/admin/inquiries/'.$inquiry->id.'/edit'));
});
