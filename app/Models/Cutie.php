<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Karyawan;
use App\Models\JenisCuti;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cutie extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cutis';
    protected $primaryKey = 'id_cuti';

    protected $fillable = [
        'karyawan_id', 'organisasi_id',  'jenis_cuti_id', 'jenis_cuti', 'durasi_cuti','rencana_mulai_cuti', 'rencana_selesai_cuti',
        'aktual_mulai_cuti','aktual_selesai_cuti','alasan_cuti','karyawan_pengganti_id','checked1_at',
        'checked1_by','checked2_at','checked2_by','approved_at','approved_by','legalized_at', 'legalized_by','rejected_at','rejected_by',
        'rejected_note','status_cuti','status_dokumen','attachment', 'penggunaan_sisa_cuti'
    ];

    public function scopeActive($query)
    {
        return $query->where('status_cuti','!=', 'CANCELED');
    }

    public function scopeOrganisasi($query, $organisasi)
    {
        if($organisasi){
            return $query->where('cutis.organisasi_id', $organisasi);
        } else {
            return $query;
        }
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }

    public function karyawanPengganti()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_pengganti_id', 'id_karyawan');
    }

    public function jenisCuti()
    {
        return $this->belongsTo(JenisCuti::class, 'jenis_cuti_id', 'id_jenis_cuti');
    }

    private static function _query($dataFilter)
    {

        $getKaryawanPengganti = Karyawan::select("id_karyawan as kp_id", "nama as nama_pengganti");
        $getJenisCuti = JenisCuti::select("id_jenis_cuti as jc_id", "jenis as jenis_cuti_khusus");
        $data = self::select(
            'id_cuti',
            'cutis.created_at',
            'rencana_mulai_cuti',
            'rencana_selesai_cuti',
            'aktual_mulai_cuti',
            'aktual_selesai_cuti',
            'durasi_cuti',
            'jenis_cuti',
            'alasan_cuti',
            'checked1_at',
            'checked2_at',
            'approved_at',
            'legalized_at',
            'checked1_by',
            'checked2_by',
            'approved_by',
            'legalized_by',
            'rejected_by',
            'rejected_at',
            'rejected_note',
            'status_dokumen',
            'status_cuti',
            'attachment',
            'kp.nama_pengganti as nama_pengganti',
            'jc.jenis_cuti_khusus as jenis_cuti_khusus',
            'karyawans.nama as nama_karyawan',
            'cutis.karyawan_id',
            'karyawan_pengganti_id',
            'departemens.nama as nama_departemen'
            )
            ->leftJoin('karyawans', 'cutis.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoinSub($getKaryawanPengganti, 'kp', function (JoinClause $joinKaryawanPengganti) {
                $joinKaryawanPengganti->on('cutis.karyawan_pengganti_id', 'kp.kp_id');
            })
            ->leftJoinSub($getJenisCuti, 'jc', function (JoinClause $joinJenisCuti) {
                $joinJenisCuti->on('cutis.jenis_cuti_id', 'jc.jc_id');
            })
            ->leftJoin('karyawan_posisi', 'cutis.karyawan_id', 'karyawan_posisi.karyawan_id')
            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
            ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen');
        
        if (isset($dataFilter['organisasi_id'])) {
            $data->where('cutis.organisasi_id', $dataFilter['organisasi_id']);
        }

        if (isset($dataFilter['karyawan_id'])) {
            $data->where('cutis.karyawan_id', $dataFilter['karyawan_id']);
        }

        if (isset($dataFilter['member_posisi_id'])) {
            $data->whereIn('posisis.id_posisi', $dataFilter['member_posisi_id']);
        }

        if(isset($dataFilter['departemen'])) {
            $data->where('departemens.id_departemen', $dataFilter['departemen']);
        }

        if(isset($dataFilter['jenisCuti'])) {
            $data->where('jenis_cuti', $dataFilter['jenisCuti']);
        }

        if(isset($dataFilter['durasi'])) {
            $data->where('durasi_cuti', $dataFilter['durasi']);
        }

        if(isset($dataFilter['statusCuti'])) {
            $data->where('status_cuti', $dataFilter['statusCuti']);
        }

        if(isset($dataFilter['statusDokumen'])) {
            $data->where('status_dokumen', $dataFilter['statusDokumen']);
        }

        if(isset($dataFilter['nama'])) {
            $data->where('karyawans.nama', 'ILIKE' , '%'.$dataFilter['nama'].'%');
        }

        if(isset($dataFilter['rencanaMulai'])) {
            $data->whereYear('rencana_mulai_cuti', Carbon::parse($dataFilter['rencanaMulai'])->year)
                ->whereMonth('rencana_mulai_cuti', Carbon::parse($dataFilter['rencanaMulai'])->month);
        }
        
        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('rencana_mulai_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('rencana_selesai_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('aktual_mulai_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('aktual_selesai_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('durasi_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('jenis_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('alasan_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('checked1_at', 'ILIKE', "%{$search}%")
                    ->orWhere('checked2_at', 'ILIKE', "%{$search}%")
                    ->orWhere('approved_at', 'ILIKE', "%{$search}%")
                    ->orWhere('legalized_at', 'ILIKE', "%{$search}%")
                    ->orWhere('rejected_at', 'ILIKE', "%{$search}%")
                    ->orWhere('status_dokumen', 'ILIKE', "%{$search}%")
                    ->orWhere('status_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('jc.jenis_cuti_khusus', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.created_at', 'ILIKE', "%{$search}%")
                    ->orWhere('departemens.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('kp.nama_pengganti', 'ILIKE', "%{$search}%");
            });
        }
        $data->groupBy('id_cuti', 'cutis.created_at', 'rencana_mulai_cuti', 'rencana_selesai_cuti', 'aktual_mulai_cuti', 'aktual_selesai_cuti', 'durasi_cuti', 'jenis_cuti', 'alasan_cuti', 'checked1_at', 'checked2_at',  'approved_at', 'legalized_at','checked1_by', 'checked2_by',  'approved_by', 'legalized_by', 'status_dokumen', 'status_cuti', 'attachment', 'kp.nama_pengganti', 'jc.jenis_cuti_khusus', 'karyawans.nama', 'cutis.karyawan_id', 'karyawan_pengganti_id','departemens.nama');

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
        return self::_query($dataFilter)->count();
    }
}
