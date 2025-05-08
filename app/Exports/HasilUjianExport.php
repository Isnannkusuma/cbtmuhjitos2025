<?php

namespace App\Exports;

use App\Models\Hasil_ujian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class HasilUjianExport implements FromCollection, WithHeadings, WithTitle
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function collection()
    {
        $hasilUjian = Hasil_ujian::with(['siswa', 'ujian'])
            ->where('id_ujian', $this->id)
            ->get(['id', 'id_ujian', 'id_siswa', 'jumlah_soal', 'jumlah_benar', 'jumlah_salah', 'nilai']);

        return $hasilUjian->map(function ($item, $index) {
            return [
                'No' => $index + 1,
                'Nama Ujian' => $item->ujian->nama_ujian,
                'Nama Siswa' => $item->siswa->nama_siswa,
                'Jumlah Soal' => $item->jumlah_soal,
                'Jumlah Benar' => $item->jumlah_benar,
                'Jumlah Salah' => $item->jumlah_salah,
                'Nilai' => $item->nilai,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Ujian',
            'Nama Siswa',
            'Jumlah Soal',
            'Jumlah Benar',
            'Jumlah Salah',
            'Nilai'
        ];
    }

    public function title(): string
    {
        return 'Hasil Ujian';
    }
}