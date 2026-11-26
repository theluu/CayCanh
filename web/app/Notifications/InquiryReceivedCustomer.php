<?php

namespace App\Notifications;

use App\Models\Inquiry;
use App\Settings\GeneralSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InquiryReceivedCustomer extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public Inquiry $inquiry) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $settings = app(GeneralSettings::class);

        return (new MailMessage)
            ->subject($settings->site_name.' đã nhận yêu cầu của bạn')
            ->greeting('Xin chào '.$this->inquiry->name.',')
            ->line('Cảm ơn bạn đã liên hệ. Chúng tôi đã nhận được yêu cầu "'.$this->inquiry->type->label().'" và sẽ liên hệ qua số '.$this->inquiry->phone.' trong thời gian sớm nhất.')
            ->lineIf((bool) $settings->hotline, 'Cần hỗ trợ gấp, vui lòng gọi hotline '.$settings->hotline.'.')
            ->salutation('Trân trọng, '.$settings->site_name);
    }
}
