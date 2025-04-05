<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\Importable;

class Soal extends Model
{
    use HasFactory, Importable;

    protected $table = 'soal';

    protected $fillable = [
        'id_ujian',
        'gambar_soal',
        'pertanyaan',
        'tipe_pertanyaan',
        'bobot',
        'tipe_jawaban',
        'pilihan_a',
        'pilihan_b',
        'pilihan_c',
        'pilihan_d',
        'pilihan_e',
        'pilihan_a_gambar',
        'pilihan_b_gambar',
        'pilihan_c_gambar',
        'pilihan_d_gambar',
        'pilihan_e_gambar',
        'jawaban',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($soal) {
            if ($soal->gambar_soal) {
                Storage::disk('public')->delete($soal->gambar_soal);
            }

            $gambarFields = ['pilihan_a_gambar', 'pilihan_b_gambar', 'pilihan_c_gambar', 'pilihan_d_gambar', 'pilihan_e_gambar'];
            foreach ($gambarFields as $field) {
                if ($soal->$field) {
                    Storage::disk('public')->delete($soal->$field);
                }
            }
        });
    }

    public function ujian()
    {
        return $this->belongsTo(Ujian::class, 'id_ujian');
    }

}
