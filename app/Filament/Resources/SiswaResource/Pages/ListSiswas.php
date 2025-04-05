<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Exports\SiswaExporter;
use App\Imports\SiswaImporter;
use App\Filament\Resources\SiswaResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;

class ListSiswas extends ListRecords
{
    protected static string $resource = SiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('example_siswa')
                ->label('Download Format Siswa')
                ->button()
                ->color('gray')
                ->action(function () {
                    $filePath = storage_path('app/public/example/example_siswa_import.xlsx');

                    if (file_exists($filePath)) {
                        return response()->download($filePath);
                    } else {
                        Notification::make()
                            ->title('Download Gagal')
                            ->danger()
                            ->body('File contoh siswa tidak ditemukan.')
                            ->send();
                    }
                }),
            ExportAction::make()
                ->label('Export Siswa')
                ->button()
                ->color('primary')
                ->outlined()
                ->exporter(SiswaExporter::class)
                ->formats([
                    ExportFormat::Xlsx,
                ]),
            Actions\Action::make('importSiswa')
                ->label('Import Siswa')
                ->button()
                ->color('primary')
                ->outlined()
                ->form([
                    FileUpload::make('file')
                        ->label('Pilih File')
                        ->required()
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv']),
                ])
                ->action(fn(array $data) => $this->importSiswa($data)),
            Actions\CreateAction::make()
                ->label('Tambah Siswa'),
        ];
    }

    public function importSiswa(array $data)
    {
        $file = $data['file'] ?? null;

        if (!$file) {
            Session::flash('error', 'Pilih file sebelum mengimpor siswa.');
            Notification::make()
                ->title('Import Gagal')
                ->danger()
                ->body('Pilih file sebelum mengimpor siswa.')
                ->send();
            return;
        }

        $filePath = storage_path('app/public/' . $file);
        if (!file_exists($filePath)) {
            Session::flash('error', 'File tidak ditemukan.');
            Notification::make()
                ->title('Import Gagal')
                ->danger()
                ->body('File tidak ditemukan.')
                ->send();
            return;
        }

        try {
            Excel::import(new SiswaImporter(), $filePath, null, \Maatwebsite\Excel\Excel::XLSX);
            unlink($filePath);
            Notification::make()
                ->title('Import Berhasil')
                ->success()
                ->body('Data siswa berhasil diimpor.')
                ->send();
        } catch (\Exception $e) {
            Log::error('Gagal mengimpor siswa', ['error' => $e->getMessage()]);
            Session::flash('error', 'Terjadi kesalahan saat mengimpor siswa.');
            Notification::make()
                ->title('Terjadi Kesalahan')
                ->danger()
                ->body('Gagal mengimpor siswa: ' . $e->getMessage())
                ->send();
        }

        Session::flash('success', 'Import berhasil!');
    }
}
