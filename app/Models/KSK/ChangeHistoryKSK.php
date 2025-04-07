<?php

namespace App\Models\KSK;

use App\Models\Posisi;
use App\Models\KSK\DetailKSK;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChangeHistoryKSK extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ksk_change_histories';
    protected $primaryKey = 'id_ksk_change_history';

    protected $fillable = [
        'ksk_detail_id',
        'changed_by_id',
        'changed_by',
        'changed_at',
        'reason',
        'status_ksk_before',
        'status_ksk_after',
        'durasi_before',
        'durasi_after',
    ];

    public function detailKSK()
    {
        return $this->belongsTo(DetailKSK::class, 'ksk_detail_id', 'id_ksk_detail');
    }

    public function changedBy()
    {
        return $this->belongsTo(Posisi::class, 'changed_by_id', 'id_posisi');
    }
}
