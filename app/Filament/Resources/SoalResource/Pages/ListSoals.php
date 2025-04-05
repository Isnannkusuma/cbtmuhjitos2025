<?php

namespace App\Filament\Resources\SoalResource\Pages;

use App\Filament\Resources\SoalResource;
use App\Models\Ujian;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SoalImporter;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;

class ListSoals extends ListRecords
{
    protected static string $resource = SoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('example_soal')
                ->label('Download Format Soal')
                ->button()
                ->color('gray')
                ->outlined()
                ->action(function () {
                    $filePath = storage_path('app/public/example/example_soal_import.xlsx');

                    if (file_exists($filePath)) {
                        return response()->download($filePath);
                    } else {
                        Notification::make()
                            ->title('Download Gagal')
                            ->danger()
                            ->body('File contoh soal tidak ditemukan.')
                            ->send();
                    }
                }),
            Actions\Action::make('importSoal')
                ->label('Import Soal')
                ->button()
                ->color('primary')
                ->outlined()
                ->form([
                    Select::make('id_ujian')
                        ->label('Pilih Ujian')
                        ->options(Ujian::pluck('nama_ujian', 'id'))
                        ->required(),
                    FileUpload::make('file')
                        ->label('Pilih File')
                        ->required()
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv']),
                ])
                ->action(fn(array $data) => $this->importSoal($data)),
            Actions\CreateAction::make()
                ->label('Tambah Soal'),
        ];
    }

    public function importSoal(array $data)
    {
        $ujianId = $data['id_ujian'] ?? null;
        $file = $data['file'] ?? null;

        if (!$ujianId || !$file) {
            Session::flash('error', 'Pilih ujian dan file sebelum mengimpor soal.');
            Notification::make()
                ->title('Import Gagal')
                ->danger()
                ->body('Pilih ujian dan file sebelum mengimpor soal.')
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
            Excel::import(new SoalImporter($ujianId), $filePath, null, \Maatwebsite\Excel\Excel::XLSX);
            unlink($filePath);
            Notification::make()
                ->title('Import Berhasil')
                ->success()
                ->body('Soal berhasil diimpor.')
                ->send();
        } catch (\Exception $e) {
            Session::flash('error', 'Terjadi kesalahan saat mengimpor soal.');
            Notification::make()
                ->title('Terjadi Kesalahan')
                ->danger()
                ->body('Gagal mengimpor soal: ' . $e->getMessage())
                ->send();
        }

        Session::flash('success', 'Import berhasil!');
    }
}
