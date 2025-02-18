<?php

namespace App\Models\TugasLuare;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasLuar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tugasluars';
    protected $primaryKey = 'id_tugasluar';
    
    protected $fillable = [
        'organisasi_id',
        'karyawan_id',
        'ni_karyawan',
        'departemen_id',
        'divisi_id',
        'created_date',
        'tanggal_pergi',
        'tanggal_kembali',
        'jenis_kendaraan',
        'no_polisi',
        'km_awal',
        'km_akhir',
        'jarak_tempuh',
        'pengemudi_id',
        'tempat_asal',
        'tempat_tujuan',
        'keterangan',
        'pembagi',
        'rate',
        'nominal',
        'millage_id',
        'checked_by',
        'checked_at',
        'legalized_by',
        'legalized_at',
        'known_by',
        'known_at'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id', 'id_organisasi');
    }

    public function pengemudi()
    {
        return $this->belongsTo(Karyawan::class, 'pengemudi_id', 'id_karyawan');
    }

    public function departemen()
    {
        return $this->belongsTo(Organisasi::class, 'departemen_id', 'id_departemen');
    }

    public function divisi()
    {
        return $this->belongsTo(Organisasi::class, 'divisi_id', 'id_divisi');
    }
}
