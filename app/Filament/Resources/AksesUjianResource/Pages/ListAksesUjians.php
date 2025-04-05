<?php

namespace App\Filament\Resources\AksesUjianResource\Pages;

use App\Filament\Resources\AksesUjianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAksesUjians extends ListRecords
{
    protected static string $resource = AksesUjianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Akses Ujian'),
        ];
    }
}
