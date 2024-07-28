<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grup extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'grups';
    protected $primaryKey = 'id_grup';

    protected $fillable = [
        'nama','jam_masuk','jam_keluar'
    ];
}
