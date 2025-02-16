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
        'nominal',
        'rencana_last_changed_by',
        'rencana_last_changed_at',
        'aktual_last_changed_by',
        'aktual_last_changed_at'
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

    //NEW
    public static function getReportMonthlyPerDepartemen($month, $year)
    {
        $subquery = DB::table('karyawans')
            ->selectRaw('
            karyawans.id_karyawan,
            posisis.jabatan_id,
            karyawans.nama,
            departemens.nama as departemen,
            posisis.nama as posisi,
            ROW_NUMBER() OVER (PARTITION BY karyawans.id_karyawan ORDER BY karyawans.id_karyawan) AS rn
            ')
            ->leftJoin('karyawan_posisi', 'karyawan_posisi.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoin('posisis', 'posisis.id_posisi', 'karyawan_posisi.posisi_id')
            ->leftJoin('departemens', 'departemens.id_departemen', 'posisis.departemen_id')
            ->groupBy('karyawans.id_karyawan', 'posisis.jabatan_id', 'karyawans.nama', 'departemens.nama', 'posisis.nama');

        $data = self::selectRaw('
            subquery.nama,
            subquery.jabatan_id,
            subquery.departemen,
            subquery.posisi,
            detail_lemburs.gaji_lembur as gaji,
            TRUNC(detail_lemburs.gaji_lembur) / detail_lemburs.pembagi_upah_lembur as upah_lembur_per_jam,
            TRUNC(SUM(TRUNC(detail_lemburs.durasi, 2) / 60),2) as total_jam_lembur,
            TRUNC(SUM(TRUNC(detail_lemburs.durasi_konversi_lembur, 2) / 60), 2) as konversi_jam_lembur,
            SUM(detail_lemburs.nominal) - SUM(detail_lemburs.uang_makan) as gaji_lembur,
            SUM(detail_lemburs.uang_makan) as uang_makan,
            SUM(detail_lemburs.nominal) as total_gaji_lembur
            ')
            ->joinSub($subquery, 'subquery', function ($join) {
            $join->on('subquery.id_karyawan', '=', 'detail_lemburs.karyawan_id');
            })
            ->leftJoin('lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')
            ->where('subquery.rn', 1)
            ->where('detail_lemburs.organisasi_id', auth()->user()->organisasi_id)
            ->whereNotNull('lemburs.actual_legalized_by')
            ->where('lemburs.status', 'COMPLETED')
            ->whereMonth('detail_lemburs.aktual_mulai_lembur', $month)
            ->whereYear('detail_lemburs.aktual_mulai_lembur', $year)
            ->groupBy('subquery.jabatan_id', 'subquery.nama', 'subquery.departemen', 'subquery.posisi', 'detail_lemburs.gaji_lembur', 'detail_lemburs.pembagi_upah_lembur')
            ->orderBy('subquery.departemen');

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

    public static function getMonthlyLemburPerDepartemen($dataFilter)
    {
        $organisasi_id = auth()->user()->organisasi_id;
        $subquery = self::selectRaw('
            detail_lemburs.organisasi_id,
            detail_lemburs.departemen_id,
            detail_lemburs.divisi_id,
            DATE(detail_lemburs.aktual_mulai_lembur) AS tanggal_lembur,
            SUM(detail_lemburs.nominal) AS total_nominal_lembur,
            SUM(detail_lemburs.durasi) AS total_durasi_lembur
        ')
        ->leftJoin('lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')
        ->where('detail_lemburs.is_aktual_approved', 'Y')
        ->where('lemburs.status', 'COMPLETED')
        ->whereNotNull('lemburs.actual_legalized_by')
        ->groupByRaw('detail_lemburs.organisasi_id, detail_lemburs.departemen_id, detail_lemburs.divisi_id, DATE(detail_lemburs.aktual_mulai_lembur)');

        $results = DB::table(DB::raw("( {$subquery->toSql()} ) as sub"))
            ->mergeBindings($subquery->getQuery());

        if(auth()->user()->hasRole('personalia') || (auth()->user()->karyawan && (auth()->user()->karyawan->posisi[0]->jabatan_id == 2 && auth()->user()->karyawan->posisi[0]->organisasi_id) || auth()->user()->karyawan->posisi[0]->jabatan_id == 5)){
            $results->where('sub.organisasi_id', $organisasi_id);
        }

        if(isset($dataFilter['tahun'])){
            $results->whereYear('sub.tanggal_lembur', $dataFilter['tahun']);
        } else {
            $results->whereYear('sub.tanggal_lembur', date('Y'));
        }

        if(isset($dataFilter['departemen'])){
            $results->whereIn('sub.departemen_id', $dataFilter['departemen']);
        }

        if(auth()->user()->hasRole('atasan') && (auth()->user()->karyawan && (auth()->user()->karyawan->posisi[0]->jabatan_id == 3 || auth()->user()->karyawan->posisi[0]->jabatan_id == 4))) {
            $posisi = auth()->user()->karyawan->posisi;
            if($posisi[0]->jabatan_id == 3){
                foreach($posisi as $p){
                    if ($p->departemen_id !== null) {
                        $departemen_id[] = $p->departemen_id;
                    }
                }
                $results->whereIn('sub.departemen_id', $departemen_id);
            } else {
                $results->where('sub.departemen_id', auth()->user()->karyawan->posisi[0]->departemen_id);
            }
        }

        $results->selectRaw('
            SUM(CASE WHEN EXTRACT(MONTH FROM tanggal_lembur) = 1 THEN total_nominal_lembur ELSE 0 END) as januari,
            SUM(CASE WHEN EXTRACT(MONTH FROM tanggal_lembur) = 2 THEN total_nominal_lembur ELSE 0 END) as februari,
            SUM(CASE WHEN EXTRACT(MONTH FROM tanggal_lembur) = 3 THEN total_nominal_lembur ELSE 0 END) as maret,
            SUM(CASE WHEN EXTRACT(MONTH FROM tanggal_lembur) = 4 THEN total_nominal_lembur ELSE 0 END) as april,
            SUM(CASE WHEN EXTRACT(MONTH FROM tanggal_lembur) = 5 THEN total_nominal_lembur ELSE 0 END) as mei,
            SUM(CASE WHEN EXTRACT(MONTH FROM tanggal_lembur) = 6 THEN total_nominal_lembur ELSE 0 END) as juni,
            SUM(CASE WHEN EXTRACT(MONTH FROM tanggal_lembur) = 7 THEN total_nominal_lembur ELSE 0 END) as juli,
            SUM(CASE WHEN EXTRACT(MONTH FROM tanggal_lembur) = 8 THEN total_nominal_lembur ELSE 0 END) as agustus,
            SUM(CASE WHEN EXTRACT(MONTH FROM tanggal_lembur) = 9 THEN total_nominal_lembur ELSE 0 END) as september,
            SUM(CASE WHEN EXTRACT(MONTH FROM tanggal_lembur) = 10 THEN total_nominal_lembur ELSE 0 END) as oktober,
            SUM(CASE WHEN EXTRACT(MONTH FROM tanggal_lembur) = 11 THEN total_nominal_lembur ELSE 0 END) as november,
            SUM(CASE WHEN EXTRACT(MONTH FROM tanggal_lembur) = 12 THEN total_nominal_lembur ELSE 0 END) as desember
        ');

        return $results->first();
    }

    public static function getWeeklyLemburPerDepartemen($dataFilter)
    {
        $organisasi_id = auth()->user()->organisasi_id;
        $subquery = self::selectRaw('
            departemens.nama,
            detail_lemburs.organisasi_id,
            detail_lemburs.departemen_id,
            detail_lemburs.divisi_id,
            DATE(detail_lemburs.aktual_mulai_lembur) AS tanggal_lembur,
            SUM(detail_lemburs.nominal) AS total_nominal_lembur,
            SUM(detail_lemburs.durasi) AS total_durasi_lembur
        ')
        ->leftJoin('lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')
        ->leftJoin('departemens', 'departemens.id_departemen', 'detail_lemburs.departemen_id')
        ->where('detail_lemburs.is_aktual_approved', 'Y')
        ->where('lemburs.status', 'COMPLETED')
        ->whereNotNull('lemburs.actual_legalized_by')
        ->groupByRaw('detail_lemburs.organisasi_id, detail_lemburs.departemen_id, detail_lemburs.divisi_id, DATE(detail_lemburs.aktual_mulai_lembur), departemens.nama');

        $results = DB::table(DB::raw("( {$subquery->toSql()} ) as sub"))
            ->mergeBindings($subquery->getQuery());

        if(auth()->user()->hasRole('personalia') || (auth()->user()->karyawan && (auth()->user()->karyawan->posisi[0]->jabatan_id == 2 && auth()->user()->karyawan->posisi[0]->organisasi_id) || auth()->user()->karyawan->posisi[0]->jabatan_id == 5)){
            $results->where('sub.organisasi_id', $organisasi_id);
        }

        if(isset($dataFilter['year'])){
            $results->whereYear('sub.tanggal_lembur', $dataFilter['year']);
        } else {
            $results->whereYear('sub.tanggal_lembur', date('Y'));
        }

        if(isset($dataFilter['month'])){
            $results->whereMonth('sub.tanggal_lembur', $dataFilter['month']);
        } else {
            $results->whereMonth('sub.tanggal_lembur', date('m'));
        }

        if(isset($dataFilter['departemen'])){
            $results->whereIn('sub.departemen_id', $dataFilter['departemen']);
        }

        if(auth()->user()->hasRole('atasan') && (auth()->user()->karyawan && (auth()->user()->karyawan->posisi[0]->jabatan_id == 3 || auth()->user()->karyawan->posisi[0]->jabatan_id == 4))) {
            $posisi = auth()->user()->karyawan->posisi;
            if($posisi[0]->jabatan_id == 3){
                foreach($posisi as $p){
                    if ($p->departemen_id !== null) {
                        $departemen_id[] = $p->departemen_id;
                    }
                }
                $results->whereIn('sub.departemen_id', $departemen_id);
            } else {
                $results->where('sub.departemen_id', auth()->user()->karyawan->posisi[0]->departemen_id);
            }
        }

        $results->whereNotNull('sub.nama');
        $results->selectRaw(
            'nama as departemen, 
            SUM(CASE WHEN EXTRACT(DAY FROM tanggal_lembur) BETWEEN 1 AND 7 THEN total_nominal_lembur ELSE 0 END) as minggu_1,
            SUM(CASE WHEN EXTRACT(DAY FROM tanggal_lembur) BETWEEN 8 AND 15 THEN total_nominal_lembur ELSE 0 END) as minggu_2,
            SUM(CASE WHEN EXTRACT(DAY FROM tanggal_lembur) BETWEEN 16 AND 23 THEN total_nominal_lembur ELSE 0 END) as minggu_3,
            SUM(CASE WHEN EXTRACT(DAY FROM tanggal_lembur) BETWEEN 24 AND 31 THEN total_nominal_lembur ELSE 0 END) as minggu_4'
        );
        $results->groupBy('nama');

        return $results->get();
    }

    public static function getCurrentMonthLemburPerDepartemen($dataFilter)
    {
        $organisasi_id = auth()->user()->organisasi_id;
        $subquery = self::selectRaw('
            departemens.nama,
            detail_lemburs.organisasi_id,
            detail_lemburs.departemen_id,
            detail_lemburs.divisi_id,
            DATE(detail_lemburs.aktual_mulai_lembur) AS tanggal_lembur,
            SUM(detail_lemburs.nominal) AS total_nominal_lembur,
            SUM(detail_lemburs.durasi) AS total_durasi_lembur
        ')
        ->leftJoin('lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')
        ->leftJoin('departemens', 'departemens.id_departemen', 'detail_lemburs.departemen_id')
        ->where('detail_lemburs.is_aktual_approved', 'Y')
        ->where('lemburs.status', 'COMPLETED')
        ->whereNotNull('lemburs.actual_legalized_by')
        ->groupByRaw('detail_lemburs.organisasi_id, detail_lemburs.departemen_id, detail_lemburs.divisi_id, DATE(detail_lemburs.aktual_mulai_lembur), departemens.nama');

        $results = DB::table(DB::raw("( {$subquery->toSql()} ) as sub"))
            ->mergeBindings($subquery->getQuery());

        if(auth()->user()->hasRole('personalia') || (auth()->user()->karyawan && (auth()->user()->karyawan->posisi[0]->jabatan_id == 2 && auth()->user()->karyawan->posisi[0]->organisasi_id) || auth()->user()->karyawan->posisi[0]->jabatan_id == 5)){
            $results->where('sub.organisasi_id', $organisasi_id);
        }

        if(isset($dataFilter['year'])){
            $results->whereYear('sub.tanggal_lembur', $dataFilter['year']);
        } else {
            $results->whereYear('sub.tanggal_lembur', date('Y'));
        }

        if(isset($dataFilter['month'])){
            $results->whereMonth('sub.tanggal_lembur', $dataFilter['month']);
        } else {
            $results->whereMonth('sub.tanggal_lembur', date('m'));
        }

        if(isset($dataFilter['departemen'])){
            $results->whereIn('sub.departemen_id', $dataFilter['departemen']);
        }

        if(auth()->user()->hasRole('atasan') && (auth()->user()->karyawan && (auth()->user()->karyawan->posisi[0]->jabatan_id == 3 || auth()->user()->karyawan->posisi[0]->jabatan_id == 4))) {
            $posisi = auth()->user()->karyawan->posisi;
            if($posisi[0]->jabatan_id == 3){
                foreach($posisi as $p){
                    if ($p->departemen_id !== null) {
                        $departemen_id[] = $p->departemen_id;
                    }
                }
                $results->whereIn('sub.departemen_id', $departemen_id);
            } else {
                $results->where('sub.departemen_id', auth()->user()->karyawan->posisi[0]->departemen_id);
            }
        }

        $results->whereNotNull('sub.nama');

        $results->selectRaw(
            'nama as departemen, 
            departemen_id as id_departemen,
            SUM(total_nominal_lembur) as total_nominal,
            SUM(total_durasi_lembur) as total_durasi'
        );
        $results->groupBy('nama', 'id_departemen');

        return $results->get();
    }

    public static function _reviewLembur($dataFilter)
    {
        $data = self::selectRaw('
            detail_lemburs.organisasi_id,
            detail_lemburs.departemen_id,
            departemens.nama as departemen,
            divisis.nama as divisi,
            organisasis.nama as organisasi,
            detail_lemburs.divisi_id,
            CASE WHEN (lemburs.status = '."'WAITING'".' AND lemburs.plan_approved_by IS NOT NULL) THEN '."'PLANNING'".' ELSE '."'ACTUAL'".' END AS status,
            DATE(detail_lemburs.rencana_mulai_lembur) AS tanggal_lembur,
            SUM(detail_lemburs.nominal) as total_nominal_lembur,
            SUM(detail_lemburs.durasi) as total_durasi_lembur,
            COUNT(detail_lemburs.karyawan_id) as total_karyawan,
            COUNT(DISTINCT detail_lemburs.lembur_id) as total_dokumen
        ')
        ->leftJoin('lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')
        ->leftJoin('departemens', 'departemens.id_departemen', 'detail_lemburs.departemen_id')
        ->leftJoin('organisasis', 'organisasis.id_organisasi', 'detail_lemburs.organisasi_id')
        ->leftJoin('divisis', 'divisis.id_divisi', 'detail_lemburs.divisi_id');

        if(isset($dataFilter['status'])){
            $status = $dataFilter['status'];

            if($status == 'PLANNING') {
            $data->where(function ($query) {
                $query->where('lemburs.status','WAITING');
                $query->whereNotNull('lemburs.plan_approved_by');
                $query->whereNull('lemburs.plan_legalized_by');
                $query->whereNull('lemburs.plan_reviewed_by');
            });
            } else {
            $data->where(function ($query) {
                $query->where('lemburs.status', 'COMPLETED');
                $query->whereNotNull('lemburs.actual_approved_by');
                $query->whereNull('lemburs.actual_legalized_by');
                $query->whereNull('lemburs.actual_reviewed_by');
            });
            }
        } else {
            $data->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('lemburs.status','WAITING');
                    $query->whereNotNull('lemburs.plan_approved_by');
                    $query->whereNull('lemburs.plan_reviewed_by');
                    $query->whereNull('lemburs.plan_legalized_by');
                });
                $query->orWhere(function ($query) {
                    $query->where('lemburs.status', 'COMPLETED');
                    $query->whereNotNull('lemburs.actual_approved_by');
                    $query->whereNull('lemburs.actual_reviewed_by');
                    $query->whereNull('lemburs.actual_legalized_by');
                });
            });
        }

        if(isset($dataFilter['departemen'])){
            $data->whereIn('detail_lemburs.departemen_id', $dataFilter['departemen']);
        } 

        if(isset($dataFilter['organisasi'])){
            $data->whereIn('detail_lemburs.organisasi_id', $dataFilter['organisasi']);
        } 

        $data->where('detail_lemburs.is_aktual_approved', 'Y')
        ->groupBy('detail_lemburs.organisasi_id', 'detail_lemburs.departemen_id', 'detail_lemburs.divisi_id', 'departemens.nama', 'divisis.nama', 'organisasis.nama', 'tanggal_lembur', 'lemburs.plan_approved_by', 'lemburs.status');

        return $data;
    }

    public static function getDataReviewLembur($dataFilter, $settings)
    {
        $data = self::_reviewLembur($dataFilter);
        $result = DB::table(DB::raw("( {$data->toSql()} ) as subquery"))
                    ->mergeBindings($data->getQuery())
                    ->offset($settings['start'])
                    ->limit($settings['limit'])
                    ->orderBy($settings['order'], $settings['dir'])
                    ->get();

        return $result;
    }

    public static function countDataReviewLembur($dataFilter)
    {
        return self::_reviewLembur($dataFilter)->get()->count();
    }
}
