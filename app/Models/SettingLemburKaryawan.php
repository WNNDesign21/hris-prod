<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SettingLemburKaryawan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'setting_lembur_karyawans';
    protected $primaryKey = 'id_setting_lembur_karyawan';

    protected $fillable = [
        'karyawan_id','organisasi_id','jabatan_id','departemen_id','gaji'
    ];
}
