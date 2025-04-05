<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditSiswa extends EditRecord
{
    protected static string $resource = SiswaResource::class;

    protected ?string $oldNisn = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->oldNisn = $this->record->nisn;
        return $data;
    }

    protected function afterSave(): void
    {
        $siswa = $this->record;

        if ($this->oldNisn && $this->oldNisn !== $siswa->nisn) {
            $user = User::where('name', $this->oldNisn)->first();

            if ($user) {
                $user->update([
                    'name' => $siswa->nisn,
                    'password' => Hash::make($siswa->nisn),
                ]);
            }
        }
    }
}
