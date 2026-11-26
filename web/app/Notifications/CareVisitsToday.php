<?php

namespace App\Notifications;

use App\Models\CareVisit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class CareVisitsToday extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  Collection<int, CareVisit>  $visits
     */
    public function __construct(public Collection $visits) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Lịch chăm sóc hôm nay ('.$this->visits->count().' công trình)')
            ->greeting('Chào '.$notifiable->name.',')
            ->line('Các công trình cần chăm sóc hôm nay '.today()->format('d/m/Y').':');

        foreach ($this->visits as $visit) {
            $contract = $visit->contract;
            $mail->line('• '.$contract->customer_name.' – '.$contract->address.($contract->phone ? ' – '.$contract->phone : ''));
        }

        return $mail->action('Mở lịch chăm sóc', url('/admin/care-visits'));
    }
}
