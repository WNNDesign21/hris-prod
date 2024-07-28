<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Divisi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'divisis';
    protected $primaryKey = 'id_divisi';

    protected $fillable = [
        'nama'
    ];
}
