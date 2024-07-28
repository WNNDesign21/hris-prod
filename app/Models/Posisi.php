<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Posisi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'posisis';
    protected $primaryKey = 'id_posisi';

    protected $fillable = [
        'jabatan_id','nama','parent'
    ];
}
