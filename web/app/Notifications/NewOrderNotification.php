<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\OrderItem;
use App\Support\Money;
use App\Support\StaffRecipients;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    /** @var array<int, int> */
    public array $backoff = [10, 60, 300];

    public function __construct(public Order $order) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return StaffRecipients::channelsFor($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('[Đơn hàng mới] '.$this->order->code.' – '.$this->order->formattedSubtotal())
            ->greeting('Có đơn hàng mới từ website');

        foreach ($this->lines() as $line) {
            $mail->line($line);
        }

        return $mail->action('Mở đơn hàng', $this->adminUrl());
    }

    public function toTelegram(object $notifiable): TelegramMessage
    {
        return TelegramMessage::create()
            ->content('🛒 *Đơn hàng mới* '.TelegramMessage::escapeLegacyMarkdown($this->order->code))
            ->line('')
            ->line(TelegramMessage::escapeLegacyMarkdown(implode("\n", $this->lines())))
            ->button('Mở đơn trong admin', $this->adminUrl());
    }

    public function toZaloOa(object $notifiable): string
    {
        return 'Đơn hàng mới '.$this->order->code."\n".implode("\n", $this->lines())."\n".$this->adminUrl();
    }

    /** @return array<int, string> */
    public function lines(): array
    {
        $order = $this->order->loadMissing('items');
        $lines = [
            'Khách: '.$order->customer_name.' – '.$order->phone,
            'Địa chỉ: '.$order->address.', '.$order->province,
            'Thanh toán: '.$order->payment_method->label(),
        ];

        foreach ($order->items as $item) {
            /** @var OrderItem $item */
            $lines[] = '• '.$item->product_name.' × '.$item->qty.' = '.Money::vnd($item->line_total);
        }

        $lines[] = 'Tổng: '.$order->formattedSubtotal().' ('.$order->items->count().' dòng)';

        if ($order->note) {
            $lines[] = 'Ghi chú: '.$order->note;
        }

        return $lines;
    }

    public function adminUrl(): string
    {
        return url('/admin/orders/'.$this->order->id.'/edit');
    }
}
