<?php

namespace App\Console\Commands;

use App\Enums\CareVisitStatus;
use App\Models\CareVisit;
use App\Models\User;
use App\Notifications\CareVisitsToday;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('care:remind')]
#[Description('Gửi email nhắc lịch chăm sóc trong ngày cho từng kỹ thuật viên')]
class SendCareReminders extends Command
{
    public function handle(): int
    {
        $visits = CareVisit::with(['contract', 'technician'])
            ->whereDate('scheduled_on', today())
            ->whereIn('status', [CareVisitStatus::Scheduled, CareVisitStatus::Rescheduled])
            ->whereNotNull('technician_id')
            ->get()
            ->groupBy('technician_id');

        foreach ($visits as $technicianVisits) {
            /** @var User $technician */
            $technician = $technicianVisits->first()->technician;
            $technician?->notify(new CareVisitsToday($technicianVisits->values()));
        }

        $this->info('Đã nhắc '.$visits->count().' kỹ thuật viên.');

        return self::SUCCESS;
    }
}
