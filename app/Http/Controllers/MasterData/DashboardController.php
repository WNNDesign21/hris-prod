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
}
