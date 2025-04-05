<?php

namespace App\Filament\Resources\GuruResource\Pages;

use App\Filament\Imports\GuruImporter;
use App\Filament\Resources\GuruResource;
use Filament\Actions;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListGurus extends ListRecords
{
    protected static string $resource = GuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->label('Import Guru')
                ->button()
                ->color('primary')
                ->outlined()
                ->importer(GuruImporter::class),
            Actions\CreateAction::make()
                ->label('Tambah Guru'),
        ];
    }
}