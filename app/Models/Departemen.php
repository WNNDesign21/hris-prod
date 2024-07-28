<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departemen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'departemens';
    protected $primaryKey = 'id_departemen';

    protected $fillable = [
        'divisi_id','nama'
    ];
}
