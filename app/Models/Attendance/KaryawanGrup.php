<?php

namespace App\Models\Attendance;

use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KaryawanGrup extends Model
{
    use HasFactory;

    protected $table = 'attendance_karyawan_grup';
    protected $primaryKey = 'id';

    protected $fillable = [
        'karyawan_id',
        'organisasi_id',
        'pin',
        'grup_id',
        'active_date',
        'toleransi_waktu',
        'jam_masuk',
        'jam_keluar',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }
}
