<?php

namespace App\Models;

use App\Models\User;
use App\Models\Divisi;
use App\Models\Departemen;
use App\Models\Organisasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Karyawan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'karyawans';

    protected $primaryKey = 'id_karyawan';

    protected $fillable = [
        'user_id',
        'organisasi_id',
        'posisi_id',
        'divisi_id',
        'departemen_id',
        'seksi_id',
        'grup_id',
        'no_ktp',
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'email',
        'no_telp',
        'gol_darah',
        'jenis_kelamin',
        'status_keluarga',
        'npwp',
        'no_bpjs_ks',
        'no_bpjs_kt',
        'jenis_kontrak',
        'status_karyawan',
        'sisa_cuti',
        'tahun_masuk',
        'tahun_keluar',
    ];
    
    protected $dates = [
        'tanggal_lahir',
        'tahun_masuk',
        'tahun_keluar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
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

    public function seksi()
    {
        return $this->belongsTo(Seksi::class, 'seksi_id', 'id_seksi');
    }

    public function posisi()
    {
        return $this->belongsTo(Posisi::class, 'posisi_id', 'id_posisi');
    }
}
