<?php

namespace App\Models\TugasLuare;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingTugasLuar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'setting_tugasluars';
    protected $primaryKey = 'id_setting_tugasluar';

    protected $fillable = [
       'organisasi_id','setting_name','value'
    ];
}
