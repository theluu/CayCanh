<?php

namespace App\Notifications;

use App\Enums\ArtAvailability;
use App\Models\Inquiry;
use App\Models\Product;
use App\Support\StaffRecipients;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class NewInquiryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    /** @var array<int, int> */
    public array $backoff = [10, 60, 300];

    public function __construct(public Inquiry $inquiry) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return StaffRecipients::channelsFor($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('['.$this->inquiry->type->label().'] '.$this->inquiry->name.' – '.$this->inquiry->phone)
            ->greeting('Có yêu cầu mới từ website');

        foreach ($this->lines() as $line) {
            $mail->line($line);
        }

        return $mail->action('Mở trong trang quản trị', $this->adminUrl());
    }

    public function toTelegram(object $notifiable): TelegramMessage
    {
        return TelegramMessage::create()
            ->content('🌿 *'.TelegramMessage::escapeLegacyMarkdown($this->inquiry->type->label()).'* #'.$this->inquiry->id)
            ->line('')
            ->line(TelegramMessage::escapeLegacyMarkdown(implode("\n", $this->lines())))
            ->button('Mở trong admin', $this->adminUrl());
    }

    public function toZaloOa(object $notifiable): string
    {
        return $this->inquiry->type->label().' #'.$this->inquiry->id."\n".implode("\n", $this->lines())."\n".$this->adminUrl();
    }

    /** @return array<int, string> */
    public function lines(): array
    {
        $inquiry = $this->inquiry->loadMissing('subject');
        $lines = [
            'Khách: '.$inquiry->name,
            'SĐT: '.$inquiry->phone,
        ];

        if ($inquiry->email) {
            $lines[] = 'Email: '.$inquiry->email;
        }

        if ($label = $inquiry->subjectLabel()) {
            $lines[] = 'Quan tâm: '.$label;
        }

        $subject = $inquiry->subject;
        if ($subject instanceof Product && $subject->isArt() && $subject->availability === ArtAvailability::Reserved) {
            $lines[] = '⚠️ Cây đang giữ cho yêu cầu #'.$subject->reserved_inquiry_id;
        }

        foreach ((array) $inquiry->meta as $key => $value) {
            $lines[] = self::metaLabel($key).': '.$value;
        }

        if ($inquiry->message) {
            $lines[] = 'Nội dung: '.$inquiry->message;
        }

        return $lines;
    }

    public function adminUrl(): string
    {
        return url('/admin/inquiries/'.$this->inquiry->id.'/edit');
    }

    private static function metaLabel(string $key): string
    {
        return match ($key) {
            'project_kind' => 'Loại công trình',
            'location' => 'Địa điểm',
            'area' => 'Diện tích',
            'budget' => 'Ngân sách',
            'timeline' => 'Thời gian',
            default => $key,
        };
    }
}
