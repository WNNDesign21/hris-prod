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
    protected $keyType = 'string';

    protected $fillable = [
        'id_ksk',
        'organisasi_id',
        'divisi_id',
        'nama_divisi',
        'departemen_id',
        'nama_departemen',
        'parent_id',
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
        'reviewed_ph_by_id',
        'reviewed_ph_by',
        'reviewed_ph_at',
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

    private static function _query($dataFilter)
    {

        $data = self::select(
            'ksk.id_ksk',
            'ksk.organisasi_id',
            'ksk.divisi_id',
            'ksk.nama_divisi',
            'ksk.departemen_id',
            'ksk.nama_departemen',
            'ksk.release_date',
            'ksk.parent_id',
            'ksk.released_by_id',
            'ksk.released_by',
            'ksk.released_at',
            'ksk.checked_by_id',
            'ksk.checked_by',
            'ksk.checked_at',
            'ksk.approved_by_id',
            'ksk.approved_by',
            'ksk.approved_at',
            'ksk.reviewed_div_by_id',
            'ksk.reviewed_div_by',
            'ksk.reviewed_div_at',
            'ksk.reviewed_ph_by_id',
            'ksk.reviewed_ph_by',
            'ksk.reviewed_ph_at',
            'ksk.reviewed_dir_by_id',
            'ksk.reviewed_dir_by',
            'ksk.reviewed_dir_at',
            'ksk.legalized_by',
            'ksk.legalized_at',
            'posisis.nama as parent_name',
        );

        $data->leftJoin('organisasis', 'ksk.organisasi_id', 'organisasis.id_organisasi');
        $data->leftJoin('posisis', 'ksk.parent_id', 'posisis.id_posisi');


        // Modul Approval Logic
        if($dataFilter['module'] == 'approval-must-approved') {
            if(auth()->user()->hasRole('personalia')) {
                $data->where('ksk.organisasi_id', auth()->user()->organisasi_id);
                $data->where(function ($query) {
                    $query->whereNotNull('ksk.reviewed_dir_by');
                    $query->whereNull('ksk.legalized_by');
                });
            } else {
                $main_posisi = [];
                foreach (auth()->user()->karyawan->posisi as $key => $value) {
                    array_push($main_posisi, $value->id_posisi);
                }
                $data->where(function ($query) use ($main_posisi){
                    $query->where(function ($query) use ($main_posisi){
                        $query->whereIn('ksk.released_by_id', $main_posisi);
                        $query->whereNull('ksk.released_by');
                    })
                    ->orWhere(function ($query) use ($main_posisi){
                        $query->whereNotNull('ksk.released_by');
                        $query->whereIn('ksk.checked_by_id', $main_posisi);
                        $query->whereNull('ksk.checked_by');
                    })
                    ->orWhere(function ($query) use ($main_posisi){
                        $query->whereNotNull('ksk.released_by');
                        $query->whereNotNull('ksk.checked_by');
                        $query->whereIn('ksk.approved_by_id', $main_posisi);
                        $query->whereNull('ksk.approved_by');
                    })
                    ->orWhere(function ($query) use ($main_posisi){
                        $query->whereNotNull('ksk.released_by');
                        $query->whereNotNull('ksk.checked_by');
                        $query->whereNotNull('ksk.approved_by');
                        $query->whereIn('ksk.reviewed_div_by_id', $main_posisi);
                        $query->whereNull('ksk.reviewed_div_by');
                    })
                    ->orWhere(function ($query) use ($main_posisi){
                        $query->whereNotNull('ksk.released_by');
                        $query->whereNotNull('ksk.checked_by');
                        $query->whereNotNull('ksk.approved_by');
                        $query->whereNotNull('ksk.reviewed_div_by');
                        $query->whereIn('ksk.reviewed_ph_by_id', $main_posisi);
                        $query->whereNull('ksk.reviewed_ph_by');
                    })
                    ->orWhere(function ($query) use ($main_posisi){
                        $query->whereNotNull('ksk.released_by');
                        $query->whereNotNull('ksk.checked_by');
                        $query->whereNotNull('ksk.approved_by');
                        $query->whereNotNull('ksk.reviewed_div_by');
                        $query->whereNotNull('ksk.reviewed_ph_by');
                        $query->whereIn('ksk.reviewed_dir_by_id', $main_posisi);
                        $query->whereNull('ksk.reviewed_dir_by');
                    });
                });
            }
        } elseif ($dataFilter['module'] == 'approval-history') {
            if(auth()->user()->hasRole('personalia')) {
                $data->where('ksk.organisasi_id', auth()->user()->organisasi_id);
                $data->where(function ($query) {
                    $query->whereNotNull('ksk.reviewed_dir_by');
                    $query->whereNotNull('ksk.legalized_by');
                });
            } else {
                $main_posisi = [];
                foreach (auth()->user()->karyawan->posisi as $key => $value) {
                    array_push($main_posisi, $value->id_posisi);
                }
                $data->where(function ($query) use ($main_posisi){
                    $query->where(function ($query) use ($main_posisi){
                        $query->whereIn('ksk.released_by_id', $main_posisi);
                        $query->whereNotNull('ksk.released_by');
                    })
                    ->orWhere(function ($query) use ($main_posisi){
                        $query->whereIn('ksk.checked_by_id', $main_posisi);
                        $query->whereNotNull('ksk.released_by');
                        $query->whereNotNull('ksk.checked_by');
                    })
                    ->orWhere(function ($query) use ($main_posisi){
                        $query->whereIn('ksk.approved_by_id', $main_posisi);
                        $query->whereNotNull('ksk.released_by');
                        $query->whereNotNull('ksk.checked_by');
                        $query->whereNotNull('ksk.approved_by');
                    })
                    ->orWhere(function ($query) use ($main_posisi){
                        $query->whereIn('ksk.reviewed_div_by_id', $main_posisi);
                        $query->whereNotNull('ksk.released_by');
                        $query->whereNotNull('ksk.checked_by');
                        $query->whereNotNull('ksk.approved_by');
                        $query->whereNotNull('ksk.reviewed_div_by');
                    })
                    ->orWhere(function ($query) use ($main_posisi){
                        $query->whereIn('ksk.reviewed_ph_by_id', $main_posisi);
                        $query->whereNotNull('ksk.released_by');
                        $query->whereNotNull('ksk.checked_by');
                        $query->whereNotNull('ksk.approved_by');
                        $query->whereNotNull('ksk.reviewed_div_by');
                        $query->whereNotNull('ksk.reviewed_ph_by');
                    })
                    ->orWhere(function ($query) use ($main_posisi){
                        $query->whereIn('ksk.reviewed_dir_by_id', $main_posisi);
                        $query->whereNotNull('ksk.released_by');
                        $query->whereNotNull('ksk.checked_by');
                        $query->whereNotNull('ksk.approved_by');
                        $query->whereNotNull('ksk.reviewed_div_by');
                        $query->whereNotNull('ksk.reviewed_ph_by');
                        $query->whereNotNull('ksk.reviewed_dir_by');
                    });
                });
            }
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('ksk.id_ksk', 'ILIKE', "%{$search}%")
                    ->orWhere('ksk.nama_divisi', 'ILIKE', "%{$search}%")
                    ->orWhere('ksk.nama_departemen', 'ILIKE', "%{$search}%")
                    ->orWhere('organisasis.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('ksk.released_by', 'ILIKE', "%{$search}%")
                    ->orWhere('ksk.checked_by', 'ILIKE', "%{$search}%")
                    ->orWhere('ksk.approved_by', 'ILIKE', "%{$search}%")
                    ->orWhere('ksk.reviewed_div_by', 'ILIKE', "%{$search}%")
                    ->orWhere('ksk.reviewed_ph_by', 'ILIKE', "%{$search}%")
                    ->orWhere('ksk.reviewed_dir_by', 'ILIKE', "%{$search}%")
                    ->orWhere('ksk.legalized_by', 'ILIKE', "%{$search}%");
            });
        }

        $result = $data;
        return $result;
    }

    public static function getData($dataFilter, $settings)
    {
        return self::_query($dataFilter)->offset($settings['start'])
            ->limit($settings['limit'])
            ->orderBy($settings['order'], $settings['dir'])
            ->get();
    }

    public static function countData($dataFilter)
    {
        return self::_query($dataFilter)->get()->count();
    }
}
