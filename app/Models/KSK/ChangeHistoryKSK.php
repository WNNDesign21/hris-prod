<?php

namespace App\Models\KSK;

use Illuminate\Database\Eloquent\Model;

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
}
