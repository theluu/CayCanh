<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\OrderItem;
use App\Settings\GeneralSettings;
use App\Support\Money;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderReceivedCustomer extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public Order $order) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $settings = app(GeneralSettings::class);
        $order = $this->order->loadMissing('items');

        $mail = (new MailMessage)
            ->subject('Xác nhận đơn hàng '.$order->code)
            ->greeting('Xin chào '.$order->customer_name.',')
            ->line('Cảm ơn bạn đã đặt hàng. Mã đơn: '.$order->code.'.');

        foreach ($order->items as $item) {
            /** @var OrderItem $item */
            $mail->line('• '.$item->product_name.' × '.$item->qty.' = '.Money::vnd($item->line_total));
        }

        return $mail
            ->line('Tạm tính: '.$order->formattedSubtotal().' (chưa gồm phí giao hàng).')
            ->line('Nhân viên sẽ gọi tới '.$order->phone.' để xác nhận đơn và phí giao hàng.')
            ->salutation('Trân trọng, '.$settings->site_name);
    }
}
