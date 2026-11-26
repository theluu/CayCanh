<?php

namespace App\Filament\Resources\CareVisits\Pages;

use App\Filament\Resources\CareVisits\CareVisitResource;
use Filament\Resources\Pages\ListRecords;

class ListCareVisits extends ListRecords
{
    protected static string $resource = CareVisitResource::class;
}
