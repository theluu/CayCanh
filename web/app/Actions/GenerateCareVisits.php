<?php

namespace App\Actions;

use App\Models\CareContract;
use Carbon\CarbonImmutable;

class GenerateCareVisits
{
    /** Creates missing visits from the start date up to min($until, ends_on); returns how many were created. */
    public function handle(CareContract $contract, CarbonImmutable $until): int
    {
        $start = CarbonImmutable::parse($contract->starts_on)->startOfDay();
        $limit = $contract->ends_on && $contract->ends_on->lessThan($until) ? CarbonImmutable::parse($contract->ends_on) : $until;
        $existing = $contract->visits()->pluck('scheduled_on')->map(fn ($date) => $date->toDateString())->flip();
        $created = 0;

        for ($index = 0; ($date = $contract->frequency->occurrence($start, $index))->lessThanOrEqualTo($limit); $index++) {
            if ($existing->has($date->toDateString())) {
                continue;
            }

            $contract->visits()->create([
                'scheduled_on' => $date,
                'technician_id' => $contract->default_technician_id,
            ]);
            $created++;
        }

        return $created;
    }
}
