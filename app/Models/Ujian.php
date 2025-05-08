<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    use HasFactory;

    protected $table = 'ujian';

    protected $fillable = [
        'id_mapel',
        'id_guru',
        'nama_ujian',
        'waktu',
    ];

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'id_mapel');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }

    public function hasilUjian()
    {
        return $this->hasMany(Hasil_ujian::class, 'id_ujian');
    }

    public function soal()
    {
        return $this->hasMany(Soal::class, 'id_ujian');
    }

}
