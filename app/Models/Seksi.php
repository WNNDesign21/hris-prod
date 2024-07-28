<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seksi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'seksis';
    protected $primaryKey = 'id_seksi';

    protected $fillable = [
        'departemen_id','nama'
    ];
}
