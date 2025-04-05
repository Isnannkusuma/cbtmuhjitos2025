<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hasil_ujian extends Model
{
    use HasFactory;

    protected $table = 'hasil_ujian';

    protected $fillable = [
        'id_ujian',
        'id_siswa',
        'jumlah_soal',
        'jumlah_benar',
        'jumlah_salah',
        'nilai',
    ];

    public function ujian()
    {
        return $this->belongsTo(Ujian::class, 'id_ujian');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }
}
