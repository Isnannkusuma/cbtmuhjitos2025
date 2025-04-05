<?php

namespace App\Filament\Resources\HasilUjianResource\Pages;

use App\Filament\Resources\HasilUjianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHasilUjians extends ListRecords
{
    protected static string $resource = HasilUjianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
