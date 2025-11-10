<?php

namespace App\Imports;

use App\Models\Soal;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SoalImporter implements ToModel, WithValidation, WithHeadingRow
{
    private ?int $selectedUjianId = null;

    public function __construct(?int $selectedUjianId = null)
    {
        $this->selectedUjianId = $selectedUjianId;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        if (!$this->selectedUjianId) {
            return null;
        }

        $pilihanA = isset($row['pilihan_a']) ? (string) $row['pilihan_a'] : null;
        $pilihanB = isset($row['pilihan_b']) ? (string) $row['pilihan_b'] : null;
        $pilihanC = isset($row['pilihan_c']) ? (string) $row['pilihan_c'] : null;
        $pilihanD = isset($row['pilihan_d']) ? (string) $row['pilihan_d'] : null;
        $pilihanE = isset($row['pilihan_e']) ? (string) $row['pilihan_e'] : null;

        return new Soal([
            'id_ujian' => $this->selectedUjianId,
            'pertanyaan' => $row['pertanyaan'] ?? null,
            'tipe_pertanyaan' => $row['tipe_pertanyaan'] ?? null,
            'bobot' => $row['bobot'] ?? null,
            'pilihan_a' => $pilihanA,
            'pilihan_b' => $pilihanB,
            'pilihan_c' => $pilihanC,
            'pilihan_d' => $pilihanD,
            'pilihan_e' => $pilihanE,
            'jawaban' => $row['jawaban'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.pertanyaan' => ['required', 'string'],
            '*.tipe_pertanyaan' => ['required', 'string', 'max:255'],
            '*.bobot' => ['required', 'numeric'],
            '*.pilihan_a' => ['required', 'string'],
            '*.pilihan_b' => ['required', 'string'],
            '*.pilihan_c' => ['nullable', 'string'],
            '*.pilihan_d' => ['nullable', 'string'],
            '*.pilihan_e' => ['nullable','string'],
            '*.jawaban' => ['required', 'string', 'max:255'],
        ];
    }
}
