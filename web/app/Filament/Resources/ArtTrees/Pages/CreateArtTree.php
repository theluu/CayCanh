<?php

namespace App\Filament\Resources\ArtTrees\Pages;

use App\Enums\ProductKind;
use App\Filament\Resources\ArtTrees\ArtTreeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArtTree extends CreateRecord
{
    protected static string $resource = ArtTreeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['kind'] = ProductKind::Art->value;

        return $data;
    }
}
