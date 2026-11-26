<?php

namespace App\Filament\Resources\CareContracts\Pages;

use App\Actions\GenerateCareVisits;
use App\Filament\Resources\CareContracts\CareContractResource;
use App\Models\CareContract;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

/**
 * @property CareContract $record
 */
class EditCareContract extends EditRecord
{
    protected static string $resource = CareContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate')
                ->label('Sinh lịch 60 ngày tới')
                ->icon('heroicon-o-calendar-days')
                ->action(function (GenerateCareVisits $generate) {
                    $count = $generate->handle($this->record, CarbonImmutable::today()->addDays(60));
                    Notification::make()->success()->title("Đã tạo {$count} lượt chăm sóc")->send();
                }),
            DeleteAction::make(),
        ];
    }
}
