<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateSiswa extends CreateRecord
{
    protected static string $resource = SiswaResource::class;

    protected function afterCreate(): void
    {
        $siswa = $this->record;

        User::create([
            'name' => $siswa->nisn,
            'password' => Hash::make($siswa->nisn),
            'role' => 'siswa',
            'id_siswa' => $siswa->id,
        ]);
    }
}
