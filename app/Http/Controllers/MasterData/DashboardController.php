<?php

namespace App\Http\Controllers\MasterData;

use App\Models\Kontrak;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Master Data - Dashboard",
            'page' => 'masterdata-dashboard',
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
        $aktif_last_update = Kontrak::where('status','EXTENDED')->where('status_change_date','ILIKE','%'.$recent_date.'%')->latest()->first();

        $terminasi = Karyawan::where('status_karyawan', 'TERMINASI')->count();
        $terminasi_last_update = Kontrak::with('karyawan')->where('status','CUTOFF')->where('status_change_date','ILIKE','%'.$recent_date.'%')->where('karyawans.status_karyawan', 'TERMINASI')->latest()->first();

        $resign = Karyawan::where('status_karyawan','RESIGN')->count();
        $resign_last_update = Kontrak::with('karyawan')->where('status','CUTOFF')->where('status_change_date','ILIKE','%'.$recent_date.'%')->where('karyawans.status_karyawan', 'RESIGN')->latest()->first();

        $pensiun = Karyawan::where('status_karyawan', 'PENSIUN')->count();
        $pensiun_last_update = Kontrak::with('karyawan')->where('status','CUTOFF')->where('status_change_date','ILIKE','%'.$recent_date.'%')->where('karyawans.status_karyawan', 'PENSIUN')->latest()->first();
        
        $data = [
            'aktif' => $aktif,
            'terminasi' => $terminasi,
            'resign' => $resign,
            'pensiun' => $pensiun,
            'aktif_last_update' => $aktif_last_update,
            'terminasi_last_update' => $terminasi_last_update,
            'resign_last_update' => $resign_last_update,
            'pensiun_last_update' => $pensiun_last_update
        ];

        return response()->json(['data' => $data],200);
    }
}
