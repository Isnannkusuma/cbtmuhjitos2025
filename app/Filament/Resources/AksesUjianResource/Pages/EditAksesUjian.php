<?php

namespace App\Filament\Resources\AksesUjianResource\Pages;

use App\Filament\Resources\AksesUjianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAksesUjian extends EditRecord
{
    protected static string $resource = AksesUjianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
