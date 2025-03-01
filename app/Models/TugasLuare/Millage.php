<?php

namespace App\Models\TugasLuare;

use App\Models\Divisi;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Organisasi;
use Illuminate\Database\Eloquent\Model;
use App\Models\TugasLuare\DetailMillage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Millage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'millages';
    protected $primaryKey = 'id_millage';
    public $incrementing = false;
    
    protected $fillable = [
        'id_millage',
        'karyawan_id',
        'organisasi_id',
        'departemen_id',
        'divisi_id',
        'nama_karyawan',
        'ni_karyawan',
        'no_polisi',
        'is_claimed',
        'checked_by',
        'checked_at',
        'legalized_by',
        'legalized_at',
        'rejected_by',
        'rejected_at'
    ];

    public function detailMillages()
    {
        return $this->hasMany(DetailMillage::class, 'millage_id', 'id_millage');
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
