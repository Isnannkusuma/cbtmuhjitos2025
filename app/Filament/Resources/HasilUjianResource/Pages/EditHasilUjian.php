<?php

namespace App\Filament\Resources\HasilUjianResource\Pages;

use App\Filament\Resources\HasilUjianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHasilUjian extends EditRecord
{
    protected static string $resource = HasilUjianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
