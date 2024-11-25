<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GajiDepartemen extends Model
{
    use HasFactory;

    protected $table = 'gaji_departemens';
    protected $primaryKey = 'id_gaji_departemen';

    protected $fillable = [
        'departemen_id','periode','total_gaji','nominal_batas_lembur'
    ];
}
