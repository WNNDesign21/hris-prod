<?php

namespace App\Models;

use App\Models\Lembure;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailLembur extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'detail_lemburs';
    protected $primaryKey = 'id_detail_lembur';

    protected $fillable = [
        'lembur_id', 
        'karyawan_id', 
        'organisasi_id', 
        'departemen_id', 
        'divisi_id', 
        'rencana_mulai_lembur', 
        'rencana_selesai_lembur', 
        'is_rencana_approved', 
        'aktual_mulai_lembur', 
        'aktual_selesai_lembur', 
        'is_aktual_approved', 
        'durasi', 
        'durasi_istirahat',
        'durasi_konversi_lembur',
        'gaji_lembur',
        'uang_makan',
        'pembagi_upah_lembur',
        'deskripsi_pekerjaan', 
        'keterangan', 
        'nominal'
    ];

    public function lembur()
    {
        return $this->belongsTo(Lembure::class, 'lembur_id', 'id_lembur');
    }

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id', 'id_organisasi');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id', 'id_departemen');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id', 'id_divisi');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }

    public static function getSlipLemburPerDepartemen($karyawan_id, $date, $organisasi_id)
    {
        // OLD
        // $data = self::select('detail_lemburs.*')
        // ->leftJoin('lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')
        // ->leftJoin('karyawans', 'karyawans.id_karyawan', 'detail_lemburs.karyawan_id')
        // ->leftJoin('departemens', 'departemens.id_departemen', 'detail_lemburs.departemen_id')
        // ->leftJoin('divisis', 'divisis.id_divisi', 'detail_lemburs.divisi_id')
        // ->leftJoin('karyawan_posisi', 'karyawan_posisi.karyawan_id', 'karyawans.id_karyawan')
        // ->leftJoin('posisis', 'posisis.id_posisi', 'karyawan_posisi.posisi_id')
        // ->leftJoin('setting_lembur_karyawans', 'setting_lembur_karyawans.karyawan_id', 'detail_lemburs.karyawan_id');

        // $data->where('detail_lemburs.organisasi_id', $organisasi_id)
        // ->whereNotNull('lemburs.actual_legalized_by')
        // ->where('lemburs.status', 'COMPLETED')
        // ->where('detail_lemburs.karyawan_id', $karyawan_id)
        // ->whereDate('detail_lemburs.aktual_mulai_lembur', $date)
        // ->orderBy('detail_lemburs.aktual_mulai_lembur');

        // return $data->get();

        // NEW
        $data = self::select('detail_lemburs.*')
        ->selectRaw('ROW_NUMBER() OVER (PARTITION BY detaiL_lemburs.id_detail_lembur ORDER BY detail_lemburs.id_detail_lembur) AS rn')
        ->leftJoin('lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')
        ->leftJoin('karyawans', 'karyawans.id_karyawan', 'detail_lemburs.karyawan_id')
        ->leftJoin('departemens', 'departemens.id_departemen', 'detail_lemburs.departemen_id')
        ->leftJoin('divisis', 'divisis.id_divisi', 'detail_lemburs.divisi_id')
        ->leftJoin('karyawan_posisi', 'karyawan_posisi.karyawan_id', 'karyawans.id_karyawan')
        ->leftJoin('posisis', 'posisis.id_posisi', 'karyawan_posisi.posisi_id')
        ->leftJoin('setting_lembur_karyawans', 'setting_lembur_karyawans.karyawan_id', 'detail_lemburs.karyawan_id');

        $data->where('detail_lemburs.organisasi_id', $organisasi_id)
        ->whereNotNull('lemburs.actual_legalized_by')
        ->where('lemburs.status', 'COMPLETED')
        ->where('detail_lemburs.karyawan_id', $karyawan_id)
        ->whereDate('detail_lemburs.aktual_mulai_lembur', $date)
        ->orderBy('detail_lemburs.aktual_mulai_lembur');

        $result = DB::table(DB::raw("( {$data->toSql()} ) as subquery"))
        ->mergeBindings($data->getQuery())
        ->where('subquery.rn', 1)
        ->get();

        return $result;
    }

    public static function getReportMonthlyPerDepartemen($month, $year)
    {
        $data = self::selectRaw('
            posisis.jabatan_id,
            karyawans.nama,
            departemens.nama as departemen,
            posisis.nama as posisi,
            detail_lemburs.gaji_lembur as gaji,
            TRUNC(detail_lemburs.gaji_lembur) / detail_lemburs.pembagi_upah_lembur as upah_lembur_per_jam,
            TRUNC(SUM(TRUNC(detail_lemburs.durasi, 2) / 60),2) as total_jam_lembur,
            TRUNC(SUM(TRUNC(detail_lemburs.durasi_konversi_lembur, 2) / 60), 2) as konversi_jam_lembur,
            SUM(detail_lemburs.nominal) - SUM(detail_lemburs.uang_makan) as gaji_lembur,
            SUM(detail_lemburs.uang_makan) as uang_makan,
            SUM(detail_lemburs.nominal) as total_gaji_lembur
        ')
        ->leftJoin('lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')
        ->leftJoin('karyawans', 'karyawans.id_karyawan', 'detail_lemburs.karyawan_id')
        ->leftJoin('departemens', 'departemens.id_departemen', 'detail_lemburs.departemen_id')
        ->leftJoin('divisis', 'divisis.id_divisi', 'detail_lemburs.divisi_id')
        ->leftJoin('karyawan_posisi', 'karyawan_posisi.karyawan_id', 'karyawans.id_karyawan')
        ->leftJoin('posisis', 'posisis.id_posisi', 'karyawan_posisi.posisi_id')

        ->where('detail_lemburs.organisasi_id', auth()->user()->organisasi_id)
        ->whereNotNull('lemburs.actual_legalized_by')
        ->where('lemburs.status', 'COMPLETED')
        ->whereMonth('detail_lemburs.aktual_mulai_lembur', $month)
        ->whereYear('detail_lemburs.aktual_mulai_lembur', $year)
        ->groupBy('posisis.jabatan_id','karyawans.nama', 'departemens.nama','detail_lemburs.departemen_id', 'posisis.nama', 'detail_lemburs.gaji_lembur', 'detail_lemburs.pembagi_upah_lembur')
        ->orderBy('detail_lemburs.departemen_id');
        
        return $data->get();
    }

    // NEW
    public static function getLeaderboardUserMonthly($dataFilter)
    {
        $subquery = self::selectRaw('
            detail_lemburs.*,
            karyawans.nama as karyawan_nama,
            departemens.nama as departemen_nama,
            divisis.nama as divisi_nama,
            ROW_NUMBER() OVER (PARTITION BY detail_lemburs.id_detail_lembur ORDER BY detail_lemburs.id_detail_lembur) AS rn
        ')
        ->leftJoin('lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')
        ->leftJoin('karyawans', 'karyawans.id_karyawan', 'detail_lemburs.karyawan_id')
        ->leftJoin('departemens', 'departemens.id_departemen', 'detail_lemburs.departemen_id')
        ->leftJoin('divisis', 'divisis.id_divisi', 'detail_lemburs.divisi_id')
        ->leftJoin('karyawan_posisi', 'karyawan_posisi.karyawan_id', 'karyawans.id_karyawan')
        ->leftJoin('posisis', 'posisis.id_posisi', 'karyawan_posisi.posisi_id')
        ->where('lemburs.status', 'COMPLETED')
        ->whereNotNull('lemburs.actual_legalized_by');

        if(auth()->user()->hasRole('personalia') || (auth()->user()->karyawan->posisi[0]->jabatan_id <= 2 && auth()->user()->karyawan->posisi[0]->organisasi_id !== null)){
            $subquery->where('detail_lemburs.organisasi_id', auth()->user()->organisasi_id);
        } elseif (auth()->user()->karyawan && auth()->user()->karyawan->posisi[0]->jabatan_id <= 2 && auth()->user()->karyawan->posisi[0]->divisi_id !== null && auth()->user()->karyawan->posisi[0]->organisasi_id == null){
            $posisis = auth()->user()->karyawan->posisi;
            $divisi_ids = [];
            foreach ($posisis as $posisi){
                if($posisi->divisi_id !== null){
                    $divisi_ids[] = $posisi->divisi_id;
                }
            }
            $subquery->whereIn('detail_lemburs.divisi_id', $divisi_ids);
        }

        if(isset($dataFilter['departemen'])){
            $subquery->where('detail_lemburs.departemen_id', $dataFilter['departemen']);
        }

        if (isset($dataFilter['member_posisi_ids'])) {
            $subquery->whereIn('posisis.id_posisi', $dataFilter['member_posisi_ids']);
        }

        $filteredData = DB::table(DB::raw("( {$subquery->toSql()} ) as subquery"))
            ->mergeBindings($subquery->getQuery())
            ->where('subquery.rn', 1);

        $data = $filteredData
            ->selectRaw('
                karyawan_nama as nama,
                departemen_nama as departemen,
                divisi_nama as divisi,
                TRUNC(SUM(TRUNC(subquery.durasi, 2) / 60),2) as total_jam_lembur,
                SUM(subquery.nominal) as total_nominal_lembur,
                DENSE_RANK() OVER (ORDER BY SUM(subquery.nominal) DESC, karyawan_nama ASC) as peringkat
            ')
            ->whereMonth('subquery.aktual_mulai_lembur', (int) $dataFilter['month'])
            ->whereYear('subquery.aktual_mulai_lembur', (int) $dataFilter['year'])
            ->groupBy('karyawan_nama', 'departemen_nama', 'divisi_nama', 'subquery.departemen_id')
            ->limit(((int) $dataFilter['limit']) ?? 50)
            ->offset(0);

        return $data->get();
    }

    private static function _query($dataFilter)
    {
        $data = self::selectRaw('
            detail_lemburs.lembur_id,
            karyawans.nama,
            posisis.nama as posisi,
            departemens.nama as departemen,
            divisis.nama as divisi,
            detail_lemburs.aktual_mulai_lembur,
            detail_lemburs.aktual_selesai_lembur,
            detail_lemburs.durasi,
            detail_lemburs.nominal,
            ROW_NUMBER() OVER (PARTITION BY detail_lemburs.id_detail_lembur ORDER BY detail_lemburs.id_detail_lembur) AS rn
        ')
        ->leftJoin('lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')
        ->leftJoin('karyawans', 'karyawans.id_karyawan', 'detail_lemburs.karyawan_id')
        ->leftJoin('departemens', 'departemens.id_departemen', 'detail_lemburs.departemen_id')
        ->leftJoin('divisis', 'divisis.id_divisi', 'detail_lemburs.divisi_id')
        ->leftJoin('karyawan_posisi', 'karyawan_posisi.karyawan_id', 'karyawans.id_karyawan')
        ->leftJoin('posisis', 'posisis.id_posisi', 'karyawan_posisi.posisi_id')

        ->where('lemburs.status', 'COMPLETED')
        ->whereNotNull('lemburs.actual_legalized_by');

        if(auth()->user()->hasRole('personalia') || (auth()->user()->karyawan->posisi[0]->jabatan_id <= 2 && auth()->user()->karyawan->posisi[0]->organisasi_id !== null)){
            $data->where('detail_lemburs.organisasi_id', auth()->user()->organisasi_id);
        } elseif (auth()->user()->karyawan && auth()->user()->karyawan->posisi[0]->jabatan_id <= 2 && auth()->user()->karyawan->posisi[0]->divisi_id !== null && auth()->user()->karyawan->posisi[0]->organisasi_id == null){
            $posisis = auth()->user()->karyawan->posisi;
            $divisi_ids = [];
            foreach ($posisis as $posisi){
                if($posisi->divisi_id !== null){
                    $divisi_ids[] = $posisi->divisi_id;
                }
            }
            $data->whereIn('detail_lemburs.divisi_id', $divisi_ids);
        }

        if (isset($dataFilter['member_posisi_ids'])) {
            $data->whereIn('posisis.id_posisi', $dataFilter['member_posisi_ids']);
        }

        if(isset($dataFilter['departemen'])){
            $data->where('detail_lemburs.departemen_id', $dataFilter['departemen']);
        }

        if (isset($dataFilter['month'])) {
            $data->whereMonth('detail_lemburs.aktual_mulai_lembur', (int) $dataFilter['month']);
        }

        if (isset($dataFilter['year'])) {
            $data->whereYear('detail_lemburs.aktual_mulai_lembur', (int) $dataFilter['year']);
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('departemens.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%");
            });
        }

        $result = DB::table(DB::raw("( {$data->toSql()} ) as subquery"))
            ->mergeBindings($data->getQuery())
            ->where('subquery.rn', 1);

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

    // OLD
    // public static function getLeaderboardUserMonthly($dataFilter)
    // {
    //     $data = self::selectRaw('
    //         karyawans.nama,
    //         departemens.nama as departemen,
    //         divisis.nama as divisi,
    //         TRUNC(SUM(TRUNC(detail_lemburs.durasi, 2) / 60),2) as total_jam_lembur,
    //         SUM(detail_lemburs.nominal) as total_nominal_lembur,
    //         DENSE_RANK() OVER (ORDER BY SUM(detail_lemburs.nominal) DESC, karyawans.nama ASC) as peringkat
    //     ')
    //     ->leftJoin('lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')
    //     ->leftJoin('karyawans', 'karyawans.id_karyawan', 'detail_lemburs.karyawan_id')
    //     ->leftJoin('departemens', 'departemens.id_departemen', 'detail_lemburs.departemen_id')
    //     ->leftJoin('divisis', 'divisis.id_divisi', 'detail_lemburs.divisi_id')
    //     ->leftJoin('karyawan_posisi', 'karyawan_posisi.karyawan_id', 'karyawans.id_karyawan')
    //     ->leftJoin('posisis', 'posisis.id_posisi', 'karyawan_posisi.posisi_id')

    //     ->where('lemburs.status', 'COMPLETED')
    //     ->whereNotNull('lemburs.actual_legalized_by');

    //     if(auth()->user()->hasRole('personalia') || (auth()->user()->karyawan->posisi[0]->jabatan_id <= 2 && auth()->user()->karyawan->posisi[0]->organisasi_id !== null)){
    //         $data->where('detail_lemburs.organisasi_id', auth()->user()->organisasi_id);
    //     } elseif (auth()->user()->karyawan && auth()->user()->karyawan->posisi[0]->jabatan_id <= 2 && auth()->user()->karyawan->posisi[0]->divisi_id !== null && auth()->user()->karyawan->posisi[0]->organisasi_id == null){
    //         $posisis = auth()->user()->karyawan->posisi;
    //         $divisi_ids = [];
    //         foreach ($posisis as $posisi){
    //             if($posisi->divisi_id !== null){
    //                 $divisi_ids[] = $posisi->divisi_id;
    //             }
    //         }
    //         $data->whereIn('detail_lemburs.divisi_id', $divisi_ids);
    //     }

    //     if(isset($dataFilter['departemen'])){
    //         $data->where('detail_lemburs.departemen_id', $dataFilter['departemen']);
    //     }

    //     if (isset($dataFilter['member_posisi_ids'])) {
    //         $data->whereIn('posisis.id_posisi', $dataFilter['member_posisi_ids']);
    //     }

    //     $data->whereMonth('detail_lemburs.aktual_mulai_lembur', (int) $dataFilter['month']);
    //     $data->whereYear('detail_lemburs.aktual_mulai_lembur', (int) $dataFilter['year']);
    //     $data->limit(((int) $dataFilter['limit']) ?? 50);
    //     $data->offset(0);

    //     $data->groupBy('karyawans.nama', 'departemens.nama', 'divisis.nama', 'detail_lemburs.departemen_id');

    //     return $data->get();
    // }

    // private static function _query($dataFilter)
    // {
    //     $data = self::selectRaw('
    //         detail_lemburs.lembur_id,
    //         karyawans.nama,
    //         posisis.nama as posisi,
    //         departemens.nama as departemen,
    //         divisis.nama as divisi,
    //         detail_lemburs.aktual_mulai_lembur,
    //         detail_lemburs.aktual_selesai_lembur,
    //         detail_lemburs.durasi,
    //         detail_lemburs.nominal
    //     ')
    //     ->leftJoin('lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')
    //     ->leftJoin('karyawans', 'karyawans.id_karyawan', 'detail_lemburs.karyawan_id')
    //     ->leftJoin('departemens', 'departemens.id_departemen', 'detail_lemburs.departemen_id')
    //     ->leftJoin('divisis', 'divisis.id_divisi', 'detail_lemburs.divisi_id')
    //     ->leftJoin('karyawan_posisi', 'karyawan_posisi.karyawan_id', 'karyawans.id_karyawan')
    //     ->leftJoin('posisis', 'posisis.id_posisi', 'karyawan_posisi.posisi_id')

    //     ->where('lemburs.status', 'COMPLETED')
    //     ->whereNotNull('lemburs.actual_legalized_by');

    //     if(auth()->user()->hasRole('personalia') || (auth()->user()->karyawan->posisi[0]->jabatan_id <= 2 && auth()->user()->karyawan->posisi[0]->organisasi_id !== null)){
    //         $data->where('detail_lemburs.organisasi_id', auth()->user()->organisasi_id);
    //     } elseif (auth()->user()->karyawan && auth()->user()->karyawan->posisi[0]->jabatan_id <= 2 && auth()->user()->karyawan->posisi[0]->divisi_id !== null && auth()->user()->karyawan->posisi[0]->organisasi_id == null){
    //         $posisis = auth()->user()->karyawan->posisi;
    //         $divisi_ids = [];
    //         foreach ($posisis as $posisi){
    //             if($posisi->divisi_id !== null){
    //                 $divisi_ids[] = $posisi->divisi_id;
    //             }
    //         }
    //         $data->whereIn('detail_lemburs.divisi_id', $divisi_ids);
    //     }

    //     if (isset($dataFilter['member_posisi_ids'])) {
    //         $data->whereIn('posisis.id_posisi', $dataFilter['member_posisi_ids']);
    //     }

    //     if(isset($dataFilter['departemen'])){
    //         $data->where('detail_lemburs.departemen_id', $dataFilter['departemen']);
    //     }

    //     if (isset($dataFilter['month'])) {
    //         $data->whereMonth('detail_lemburs.aktual_mulai_lembur', (int) $dataFilter['month']);
    //     }

    //     if (isset($dataFilter['year'])) {
    //         $data->whereYear('detail_lemburs.aktual_mulai_lembur', (int) $dataFilter['year']);
    //     }

    //     if (isset($dataFilter['search'])) {
    //         $search = $dataFilter['search'];
    //         $data->where(function ($query) use ($search) {
    //             $query->where('departemens.nama', 'ILIKE', "%{$search}%")
    //                 ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%");
    //         });
    //     }

    //     $result = $data;
    //     return $result;
    // }

    // public static function getData($dataFilter, $settings)
    // {
    //     return self::_query($dataFilter)->offset($settings['start'])
    //         ->limit($settings['limit'])
    //         ->orderBy($settings['order'], $settings['dir'])
    //         ->get();
    // }

    // public static function countData($dataFilter)
    // {
    //     return self::_query($dataFilter)->get()->count();
    // }

    public static function generateLemburHarian()
    {
        //RAW QUERY NYA 
        // SELECT
        //     detail_lemburs.organisasi_id,
        //     detail_lemburs.departemen_id,
        //     detail_lemburs.divisi_id,
        //     DATE(detail_lemburs.aktual_mulai_lembur) AS tanggal_lembur,
        //     SUM(detail_lemburs.nominal) as total_nominal_lembur,
        //     SUM(detail_lemburs.durasi) as total_durasi_lembur
        // FROM detail_lemburs
        // LEFT JOIN lemburs ON lemburs.id_lembur = detail_lemburs.lembur_id
        // WHERE detail_lemburs.is_aktual_approved = 'Y'
        // AND lemburs.status = 'COMPLETED' AND lemburs.actual_legalized_by IS NOT NULL
        // GROUP BY
        //     detail_lemburs.organisasi_id,
        //     detail_lemburs.departemen_id,
        //     detail_lemburs.divisi_id,
        //     DATE(detail_lemburs.aktual_mulai_lembur)	
        // ORDER BY tanggal_lembur ASC;
        
        $data = self::selectRaw('
            detail_lemburs.organisasi_id,
            detail_lemburs.departemen_id,
            detail_lemburs.divisi_id,
            DATE(detail_lemburs.aktual_mulai_lembur) AS tanggal_lembur,
            SUM(detail_lemburs.nominal) as total_nominal_lembur,
            SUM(detail_lemburs.durasi) as total_durasi_lembur
        ')
        ->leftJoin('lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')

        ->where('detail_lemburs.is_aktual_approved', 'Y')
        ->where('lemburs.status', 'COMPLETED')
        ->whereNotNull('lemburs.actual_legalized_by')
        ->groupByRaw('detail_lemburs.organisasi_id, detail_lemburs.departemen_id, detail_lemburs.divisi_id, DATE(detail_lemburs.aktual_mulai_lembur)')
        ->orderBy('tanggal_lembur', 'ASC')
        ->get();

        return $data;
    }
}
