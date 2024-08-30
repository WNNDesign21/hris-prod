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
        $jumlah_karyawan_keluar = Turnover::whereIN('status_karyawan', ['RESIGN', 'PENSIUN', 'TERMINASI'])
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
        $recent_date = $year.'-'.$month;
        $aktif = Karyawan::where('status_karyawan', 'AKTIF')->count();
        $terminasi = Karyawan::where('status_karyawan', 'TERMINASI')->count();
        $resign = Karyawan::where('status_karyawan','RESIGN')->count();
        $pensiun = Karyawan::where('status_karyawan', 'PENSIUN')->count();
        
        $data = [
            'aktif' => $aktif,
            'terminasi' => $terminasi,
            'resign' => $resign,
            'pensiun' => $pensiun,
        ];

        return response()->json(['data' => $data],200);
    }

    public function get_data_turnover_monthly_dashboard(string $year = ''){
        // $year = $year == '' ? date('Y') : $year;

        //Data Turnover perbulan dalam tahun berjalan
        $query = "
        WITH KaryawanKeluar AS (
                SELECT
                    EXTRACT(MONTH FROM tanggal_keluar) AS bulan,
                    COUNT(*) AS jumlah_karyawan_keluar
                FROM
                    turnovers
                WHERE
                    status_karyawan IN ('RESIGN', 'TERMINASI', 'PENSIUN')
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

    public function get_data_kontrak_progress_dashboard(){
        $total_kontrak = Kontrak::count();
        $kontrak_done = Kontrak::where('status', 'DONE')->count();
        $presentase_kontrak_done = ($kontrak_done / $total_kontrak) * 100;

        return response()->json(['data' => $presentase_kontrak_done], 200);
    }
}
