<?php

namespace App\Models\KSK;

use App\Models\Divisi;
use App\Models\Departemen;
use App\Models\Organisasi;
use App\Models\KSK\DetailKSK;
use App\Models\KSK\ChangeHistoryKSK;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KSK extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ksk';
    protected $primaryKey = 'id_ksk';
    public $incrementing = false;
    
    protected $fillable = [
        'id_ksk',
        'organisasi_id',
        'divisi_id',
        'nama_divisi',
        'departemen_id',
        'nama_departemen',
        'release_date',
        'released_by_id',
        'released_by',
        'released_at',
        'checked_by_id',
        'checked_by',
        'checked_at',
        'approved_by_id',
        'approved_by',
        'approved_at',
        'reviewed_div_by_id',
        'reviewed_div_by',
        'reviewed_div_at',
        'reviewed_dir_by_id',
        'reviewed_dir_by',
        'reviewed_dir_at',
        'legalized_by',
        'legalized_at',
    ];

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id', 'id_organisasi');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id', 'id_divisi');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id', 'id_departemen');
    }

    public function detailKSK()
    {
        return $this->hasMany(DetailKSK::class, 'ksk_id', 'id_ksk');
    }

    public function changeHistoryKSK()
    {
        return $this->hasMany(ChangeHistoryKSK::class, 'ksk_detail_id', 'id_ksk_detail');
    }
}
