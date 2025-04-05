<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Hash;

class SiswaImporter implements ToModel, WithHeadingRow, WithValidation
{

    public function startRow(): int
    {
        return 2;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if (empty($row['nisn']) || empty($row['nama_siswa']) || empty($row['kelas'])) {
            return null;
        }

        $existingSiswa = Siswa::where('nisn', $row['nisn'])->first();
        if ($existingSiswa) {
            return null;
        }

        $siswa = Siswa::create([
            'nisn'        => $row['nisn'] ?? null,
            'nama_siswa'  => $row['nama_siswa'] ?? null,
            'kelas'       => $row['kelas'] ?? null,
        ]);

        User::create([
            'name'     => $siswa->nisn,
            'password' => Hash::make($siswa->nisn),
            'role'     => 'siswa',
            'id_siswa' => $siswa->id,
        ]);

        return $siswa;
    }

    /**
     * Menentukan aturan validasi untuk setiap baris
     * 
     * @return array
     */
    public function rules(): array
    {
        return [
            '*.nisn'        => ['required', 'unique:siswa,nisn'],
            '*.nama_siswa'  => ['required', 'string', 'max:255'],
            '*.kelas'       => ['required', 'string', 'max:50'],
        ];
    }
}
