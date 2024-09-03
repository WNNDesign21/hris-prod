<?php

namespace App\Models;

use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cutie extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cutis';
    protected $primaryKey = 'id_cuti';

    protected $fillable = [
        'karyawan_id', 'jenis_cuti_id', 'durasi_cuti','rencana_mulai_cuti', 'rencana_selesai_cuti',
        'aktual_mulai_cuti','aktual_selesai_cuti','alasan_cuti','karyawan_pengganti_id','checked_at',
        'checked_by','approved_at','approved_by','legalize_at', 'legalize_by','rejected_at','rejected_by',
        'rejected_note','status_cuti','isCompleted','attachment'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }
}
