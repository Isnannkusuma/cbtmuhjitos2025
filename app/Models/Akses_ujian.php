<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akses_ujian extends Model
{
    use HasFactory;

    protected $table = 'akses_ujian';

    protected $fillable = [
        'id_siswa',
        'id_ujian',
        'status',
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
