<?php

namespace App\Http\Controllers\MasterData;

use App\Models\Kontrak;
use App\Models\Karyawan;
use App\Models\Turnover;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jumlah_karyawan_keluar = Turnover::whereIN('status_karyawan', ['MD', 'PS', 'HK', 'TM'])
            ->whereYear('tanggal_keluar', date('Y'))
            ->count();
        $dataPage = [
            'pageTitle' => "Master Data - Dashboard",
            'page' => 'masterdata-dashboard',
            'jumlah_karyawan_keluar' => $jumlah_karyawan_keluar
        ];
        return view('pages.master-data.index', $dataPage);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function get_data_karyawan_dashboard(){
        $year = date('Y');
        $month = date('m');
        $aktif = Karyawan::where('status_karyawan', 'AT')->count();
        $habis_kontrak = Turnover::where('status_karyawan', 'HK')->whereYear('tanggal_keluar', $year)->whereMonth('tanggal_keluar', $month)->count();
        $mengundurkan_diri = Turnover::where('status_karyawan', 'MD')->whereYear('tanggal_keluar', $year)->whereMonth('tanggal_keluar', $month)->count();
        $pensiun = Turnover::where('status_karyawan', 'PS')->whereYear('tanggal_keluar', $year)->whereMonth('tanggal_keluar', $month)->count();
        $terminasi = Turnover::where('status_karyawan', 'TM')->whereYear('tanggal_keluar', $year)->whereMonth('tanggal_keluar', $month)->count();
        
        $data = [
            'aktif' => $aktif,
            'habis_kontrak' => $habis_kontrak,
            'mengundurkan_diri' => $mengundurkan_diri,
            'pensiun' => $pensiun,
            'terminasi' => $terminasi
        ];

        return response()->json(['data' => $data],200);
    }

    public function get_data_turnover_monthly_dashboard(string $year = ''){
        //Data Turnover perbulan dalam tahun berjalan
        $query = "
        WITH KaryawanKeluar AS (
                SELECT
                    EXTRACT(MONTH FROM tanggal_keluar) AS bulan,
                    COUNT(*) AS jumlah_karyawan_keluar
                FROM
                    turnovers
                WHERE
                    status_karyawan IN ('MD', 'HK', 'PS', 'TM')
                    AND EXTRACT(YEAR FROM tanggal_keluar) = EXTRACT(YEAR FROM NOW())
                GROUP BY
                    bulan
            ),
            RataRataKaryawan AS (
                SELECT
                    (
                        (SELECT CAST(jumlah_aktif_karyawan_terakhir AS DECIMAL) FROM turnovers ORDER BY tanggal_keluar DESC LIMIT 1) +
                        (SELECT CAST(jumlah_aktif_karyawan_terakhir AS DECIMAL) FROM turnovers ORDER BY tanggal_keluar ASC LIMIT 1)
                    ) / 2.0 AS rata_rata_karyawan
            ),
            SemuaBulan AS (
			  SELECT generate_series(1, 12) AS bulans
			)
			SELECT
			  sb.bulans,
			  COALESCE(CAST(k.jumlah_karyawan_keluar AS DECIMAL) / r.rata_rata_karyawan * 100.0, 0) AS turnover
			FROM
			  SemuaBulan sb
			  LEFT JOIN KaryawanKeluar k ON sb.bulans = k.bulan
			  CROSS JOIN RataRataKaryawan r
			ORDER BY
			  sb.bulans;
        ";

        $results = DB::select($query);
        $data = [];
        foreach ($results as $key => $value) {
            $results[$key]->turnover = number_format($value->turnover, 2);
            array_push($data, $results[$key]->turnover);
        }
        return response()->json(['data' => $data],200);
    }

    public function get_data_turnover_detail_monthly_dashboard(string $year = ''){
        //Data Turnover Detail perbulan dalam tahun berjalan
        $data['mengundurkan_diri'] = [];
        $data['habis_kontrak'] = [];
        $data['pensiun'] = [];
        $data['terminasi'] = [];
        $data['masuk'] = [];

        $month = date('m');
        $year = date('Y');
        $month_array = ['01','02','03','04','05','06','07','08','09','10','11','12'];


        for ($i = 0; $i <= 11; $i++) {
            $mengundurkan_diriCount = Turnover::where('status_karyawan', 'MD')
                ->whereYear('tanggal_keluar', $year)
                ->whereMonth('tanggal_keluar', $month_array[$i])
                ->count();
            $habis_kontrakCount = Turnover::where('status_karyawan', 'HK')
                ->whereYear('tanggal_keluar', $year)
                ->whereMonth('tanggal_keluar', $month_array[$i])
                ->count();
            $pensiunCount = Turnover::where('status_karyawan', 'PS')
                ->whereYear('tanggal_keluar', $year)
                ->whereMonth('tanggal_keluar', $month_array[$i])
                ->count();
            $terminasiCount = Turnover::where('status_karyawan', 'TM')
                ->whereYear('tanggal_keluar', $year)
                ->whereMonth('tanggal_keluar', $month_array[$i])
                ->count();
            $masukCount = Karyawan::where('status_karyawan', 'AT')
                ->whereYear('tanggal_mulai', $year)
                ->whereMonth('tanggal_mulai', $month_array[$i])
                ->count();

            $data['mengundurkan_diri'][] = $mengundurkan_diriCount;
            $data['habis_kontrak'][] = $habis_kontrakCount;
            $data['pensiun'][] = $pensiunCount;
            $data['terminasi'][] = $terminasiCount;
            $data['masuk'][] = $masukCount;
        }

        return response()->json(['data' => $data],200);
    }

    public function get_data_kontrak_progress_dashboard(){
        $total_kontrak = Kontrak::count();
        $kontrak_done = Kontrak::where('status', 'DONE')->count();
        $presentase_kontrak_done = round(($kontrak_done / $total_kontrak) * 100, 2);

        return response()->json(['data' => $presentase_kontrak_done], 200);
    }

    public function get_data_keluar_masuk_karyawan_dashboard()
    {
        $year = date('Y');
         $query = "
            WITH SemuaBulan AS (
                SELECT generate_series(1, 12) AS bulan
            )
            SELECT
                sb.bulan,
                COALESCE(kk.karyawan_keluar, 0) AS karyawan_keluar,
                COALESCE(km.karyawan_masuk, 0) AS karyawan_masuk
            FROM
                SemuaBulan sb
                LEFT JOIN (
                    SELECT
                        EXTRACT(MONTH FROM tanggal_keluar) AS bulan,
                        COUNT(*) AS karyawan_keluar
                    FROM
                        turnovers
                    WHERE
                        status_karyawan IN ('MD', 'PS', 'HK', 'TM')
                        AND EXTRACT(YEAR FROM tanggal_keluar) = $year
                    GROUP BY
                        bulan
                ) kk ON sb.bulan = kk.bulan
                LEFT JOIN (
                    SELECT
                        EXTRACT(MONTH FROM tanggal_mulai) AS bulan,
                        COUNT(*) AS karyawan_masuk
                    FROM
                        karyawans
                    WHERE
                        status_karyawan = 'AT'
                        AND EXTRACT(YEAR FROM tanggal_mulai) = $year
                    GROUP BY
                        bulan
                ) km ON sb.bulan = km.bulan;
        ";

        $results = DB::select($query);
        $data = [];

        foreach ($results as $result) {
            $data['karyawan_masuk'][$result->bulan - 1] = $result->karyawan_masuk;
            $data['karyawan_keluar'][$result->bulan - 1] = $result->karyawan_keluar;
        }

        return response()->json(['data' => $data], 200);
    }

    public function get_total_data_karyawan_by_status_karyawan_dashboard(){
        $total_karyawan_reactive = Kontrak::where('status', 'DONE')->where('isReactive', 'Y')->count();
        $total_karyawan_habis_kontrak = Turnover::where('status_karyawan', 'HK')->count();
        $total_karyawan_mengundurkan_diri = Turnover::where('status_karyawan', 'MD')->count();
        $total_karyawan_pensiun = Turnover::where('status_karyawan', 'PS')->count();
        $total_karyawan_terminasi = Turnover::where('status_karyawan', 'TM')->count();

        $data = [$total_karyawan_reactive, $total_karyawan_habis_kontrak, $total_karyawan_mengundurkan_diri, $total_karyawan_pensiun, $total_karyawan_terminasi];
        return response()->json(['data' => $data], 200);
    }

}
