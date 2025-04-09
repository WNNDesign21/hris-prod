<?php

namespace App\Models\KSK;

use App\Models\KSK\Cleareance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CleareanceDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cleareance_details';
    protected $primaryKey = 'id_cleareance_detail';

    protected $fillable = [
        'cleareance_id',
        'organisasi_id',
        'type',
        'is_clear',
        'keterangan',
        'confirmed_by_id',
        'confirmed_by',
        'confirmed_at',
    ];

    public function cleareance()
    {
        return $this->belongsTo(Cleareance::class, 'cleareance_id', 'id_cleareance');
    }
}
