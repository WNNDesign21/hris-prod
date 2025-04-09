<?php

namespace App\Models\KSK;

use App\Models\Divisi;
use App\Models\Posisi;
use App\Models\Jabatan;
use App\Models\Kontrak;
use App\Models\KSK\KSK;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Organisasi;
use App\Models\KSK\ChangeHistoryKSK;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailKSK extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ksk_details';
    protected $primaryKey = 'id_ksk_detail';

    protected $fillable = [
        'ksk_id',
        'organisasi_id',
        'divisi_id',
        'nama_divisi',
        'departemen_id',
        'nama_departemen',
        'karyawan_id',
        'ni_karyawan',
        'nama_karyawan',
        'posisi_id',
        'nama_posisi',
        'jabatan_id',
        'nama_jabatan',
        'jenis_kontrak',
        'jumlah_surat_peringatan',
        'jumlah_sakit',
        'jumlah_izin',
        'jumlah_alpa',
        'status_ksk',
        'tanggal_renewal_kontrak',
        'durasi_renewal',
        'cleareance_id',
        'kontrak_id',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }

    public function kontrak()
    {
        return $this->hasMany(Kontrak::class, 'karyawan_id', 'karyawan_id');
    }

    public function ksk()
    {
        return $this->belongsTo(KSK::class, 'ksk_id', 'id_ksk');
    }

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id', 'id_organisasi');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id', 'id_divisi');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id', 'id_departemen');
    }

    public function posisi()
    {
        return $this->belongsTo(Posisi::class, 'posisi_id', 'id_posisi');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id', 'id_jabatan');
    }

    public function changeHistoryKSK()
    {
        return $this->hasMany(ChangeHistoryKSK::class, 'ksk_detail_id', 'id_ksk_detail');
    }
}
