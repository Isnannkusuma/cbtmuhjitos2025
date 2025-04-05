<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    use HasFactory;

    protected $table = 'mapel';

    protected $fillable = [
        'nama_mapel',
        'kelas',
    ];

    public function ujian()
    {
        return $this->hasMany(Ujian::class, 'id_mapel');
    }
    
}
