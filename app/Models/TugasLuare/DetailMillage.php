<?php

namespace App\Models\TugasLuare;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailMillage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'detail_millages';
    protected $primaryKey = 'id_detail_millage';
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
        'rejected_at',
        'millage_id',
        'type',
        'attachment',
        'nominal',
        'is_active'
    ];
}
