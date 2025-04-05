<?php

namespace App\Filament\Imports;

use App\Models\Guru;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class GuruImporter extends Importer
{
    protected static ?string $model = Guru::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nama_mapel')
                ->label('Nama Mapel')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('kelas')
                ->label('Kelas')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): ?Guru
    {
        // return Guru::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Guru();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your guru import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
