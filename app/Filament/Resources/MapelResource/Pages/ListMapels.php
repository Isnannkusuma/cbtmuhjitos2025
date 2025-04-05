<?php

namespace App\Filament\Resources\MapelResource\Pages;

use App\Filament\Imports\MapelImporter;
use App\Filament\Resources\MapelResource;
use Filament\Actions;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListMapels extends ListRecords
{
    protected static string $resource = MapelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->label('Import Mapel')
                ->button()
                ->color('primary')
                ->outlined()
                ->importer(MapelImporter::class),
            Actions\CreateAction::make()
                ->label('Tambah Mapel'),
        ];
    }
}
