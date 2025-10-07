<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeluargaKaryawan extends Model
{
    use HasFactory;

    protected $table = 'keluarga_karyawans';

    protected $fillable = [
        'karyawan_id',
        'hubungan',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
    ];

    /**
     * Get the karyawan that owns the keluarga.
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }
}