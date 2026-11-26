<?php

namespace App\Console\Commands;

use App\Actions\GenerateCareVisits;
use App\Models\CareContract;
use Carbon\CarbonImmutable;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('care:generate {--days=60 : Số ngày tới cần sinh lịch}')]
#[Description('Sinh các lượt chăm sóc cho hợp đồng đang hoạt động')]
class GenerateCareVisitsCommand extends Command
{
    public function handle(GenerateCareVisits $generate): int
    {
        $until = CarbonImmutable::today()->addDays((int) $this->option('days'));
        $total = 0;

        CareContract::active()->each(function (CareContract $contract) use ($generate, $until, &$total) {
            $total += $generate->handle($contract, $until);
        });

        $this->info("Đã tạo {$total} lượt chăm sóc.");

        return self::SUCCESS;
    }
}
