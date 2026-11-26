<?php

namespace App\Filament\Resources\CareContracts\Pages;

use App\Filament\Resources\CareContracts\CareContractResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCareContracts extends ListRecords
{
    protected static string $resource = CareContractResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
