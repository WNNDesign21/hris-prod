<?php

namespace App\Models\KSK;

use App\Models\Karyawan;
use App\Models\Organisasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CleareanceSetting extends Model
{
    use HasFactory;

    protected $table = 'cleareance_settings';
    protected $primaryKey = 'id_cleareance_setting';

    protected $fillable = [
        'organisasi_id',
        'type',
        'karyawan_id',
        'ni_karyawan',
        'nama_karyawan',
        'signature',
    ];

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id', 'id_organisasi');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }
}
