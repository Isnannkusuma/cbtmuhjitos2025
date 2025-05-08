<?php

namespace App\Filament\Resources\AksesUjianResource\Pages;

use App\Filament\Resources\AksesUjianResource;
use App\Models\Akses_ujian;
use App\Models\Siswa;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Illuminate\Support\Facades\Log;

class ListAksesUjians extends ListRecords
{
    protected static string $resource = AksesUjianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Tambah')
                ->label('Tambah Akses')
                ->form([
                    Forms\Components\Select::make('id_kelas')
                        ->label('Pilih Kelas')
                        ->options(function () {
                            return \App\Models\Siswa::distinct('kelas')->pluck('kelas', 'kelas');
                        })
                        ->required()
                        ->reactive(),

                    Forms\Components\Select::make('id_ujian')
                        ->label('Nama Ujian')
                        ->options(function () {
                            return \App\Models\Ujian::pluck('nama_ujian', 'id')->toArray();
                        })
                        ->required(),

                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'not_started' => 'Belum Dimulai',
                            'can_start' => 'Start',
                            'completed' => 'Selesai',
                        ])
                        ->required(),
                ])
                ->action(function (array $data) {
                    $siswaInClass = Siswa::where('kelas', $data['id_kelas'])->get();

                    foreach ($siswaInClass as $siswa) {
                        $existingRecord = Akses_ujian::where([
                            'id_siswa' => $siswa->id,
                            'id_ujian' => $data['id_ujian'],
                        ])->exists();

                        if ($existingRecord) {
                            continue;
                        }

                        Akses_ujian::create([
                            'id_siswa' => $siswa->id,
                            'id_ujian' => $data['id_ujian'],
                            'status' => $data['status'],
                        ]);
                    }
                }),
        ];
    }
}
