<?php

namespace App\Models\TugasLuare;

use App\Models\Divisi;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Organisasi;
use App\Models\TugasLuare\TugasLuar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengikutTugasLuar extends Model
{
    use HasFactory;

    protected $table = 'pengikut_tugasluars';
    protected $primaryKey = 'id_pengikut_tugasluar';

    protected $fillable = [
        'tugasluar_id',
        'karyawan_id',
        'organisasi_id',
        'departemen_id',
        'divisi_id',
        'ni_karyawan',
        'pin',
        'created_date',
        'is_active'
    ];

    public function tugasluar()
    {
        return $this->belongsTo(TugasLuar::class, 'tugasluar_id', 'id_tugasluar');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id', 'id_organisasi');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id', 'id_departemen');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id', 'id_divisi');
    }
}
