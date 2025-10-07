<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResetCuti extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reset_cutis';
    protected $primaryKey = 'id_reset_cuti';

    protected $fillable = [
        'reset_at','reset_count'
    ];
}
