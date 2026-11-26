<?php

namespace App\Listeners;

use App\Events\InquirySubmitted;
use App\Events\OrderPlaced;
use App\Notifications\InquiryReceivedCustomer;
use App\Notifications\NewInquiryNotification;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderReceivedCustomer;
use App\Support\StaffRecipients;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

/**
 * The record is already saved when this runs; a notification failure must never reach the customer.
 */
class NotifyStaff
{
    public function handleInquirySubmitted(InquirySubmitted $event): void
    {
        $this->safely(fn () => Notification::send(StaffRecipients::route(), new NewInquiryNotification($event->inquiry)));

        if ($event->inquiry->email) {
            $this->safely(fn () => Notification::route('mail', $event->inquiry->email)->notify(new InquiryReceivedCustomer($event->inquiry)));
        }
    }

    public function handleOrderPlaced(OrderPlaced $event): void
    {
        $this->safely(fn () => Notification::send(StaffRecipients::route(), new NewOrderNotification($event->order)));

        if ($event->order->email) {
            $this->safely(fn () => Notification::route('mail', $event->order->email)->notify(new OrderReceivedCustomer($event->order)));
        }
    }

    private function safely(callable $send): void
    {
        try {
            $send();
        } catch (Throwable $exception) {
            Log::error('Gửi thông báo thất bại: '.$exception->getMessage(), ['exception' => $exception]);
        }
    }
}
