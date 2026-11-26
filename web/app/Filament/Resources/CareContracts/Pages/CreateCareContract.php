<?php

namespace App\Filament\Resources\CareContracts\Pages;

use App\Actions\GenerateCareVisits;
use App\Filament\Resources\CareContracts\CareContractResource;
use App\Models\CareContract;
use App\Models\Inquiry;
use Carbon\CarbonImmutable;
use Filament\Resources\Pages\CreateRecord;

/**
 * @property CareContract $record
 */
class CreateCareContract extends CreateRecord
{
    protected static string $resource = CareContractResource::class;

    protected function fillForm(): void
    {
        parent::fillForm();

        if ($inquiry = Inquiry::find(request()->integer('inquiry'))) {
            $this->form->fill([
                ...$this->form->getRawState(),
                'inquiry_id' => $inquiry->id,
                'customer_name' => $inquiry->name,
                'phone' => $inquiry->phone,
                'address' => $inquiry->address ?? $inquiry->meta['location'] ?? null,
            ]);
        }
    }

    protected function afterCreate(): void
    {
        app(GenerateCareVisits::class)->handle($this->record, CarbonImmutable::today()->addDays(60));
    }
}
