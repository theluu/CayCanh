<?php

namespace App\Filament\Resources\ArtTrees\Pages;

use App\Filament\Resources\ArtTrees\ArtTreeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditArtTree extends EditRecord
{
    protected static string $resource = ArtTreeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
