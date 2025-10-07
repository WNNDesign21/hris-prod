<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SettingLembur extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'setting_lemburs';
    protected $primaryKey = 'id_setting_lembur';

    protected $fillable = [
       'organisasi_id','setting_name','value'
    ];
}
