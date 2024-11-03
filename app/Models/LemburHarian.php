<?php

namespace App\Models;

use App\Models\Divisi;
use App\Models\Departemen;
use App\Models\Organisasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LemburHarian extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lembur_harians';
    protected $primaryKey = 'id_lembur_harian';

    protected $fillable = [
        'organisasi_id',
        'departemen_id',
        'divisi_id',
        'total_durasi_lembur',
        'total_nominal_lembur',
        'tanggal_lembur',
    ];

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
