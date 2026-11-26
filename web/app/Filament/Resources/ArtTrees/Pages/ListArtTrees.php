<?php

namespace App\Filament\Resources\ArtTrees\Pages;

use App\Filament\Resources\ArtTrees\ArtTreeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArtTrees extends ListRecords
{
    protected static string $resource = ArtTreeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
