<?php

namespace App\Models;

use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kontrak extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kontraks';
    
    protected $primaryKey = 'id_kontrak';
    public $incrementing = false;

    protected $fillable = [
        'id_kontrak',
        'karyawan_id',
        'nama_posisi',
        'jenis',
        'status',
        'durasi',
        'salary',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_selesai',
        'isAccepted',
        'attachment'
    ];
    
    protected $dates = [
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }
}
