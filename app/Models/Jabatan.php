<?php

namespace App\Models;

use App\Models\Posisi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jabatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jabatans';
    protected $primaryKey = 'id_jabatan';

    protected $fillable = [
        'nama'
    ];

    public function posisis()
    {
        return $this->hasMany(Posisi::class, 'jabatan_id', 'id_jabatan');
    }
}
