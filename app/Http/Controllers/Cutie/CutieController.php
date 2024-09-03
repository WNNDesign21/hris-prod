<?php

namespace App\Http\Controllers\Cutie;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CutieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Cutie - Dashboard",
            'page' => 'cutie-dashboard',
        ];
        return view('pages.cuti-e.index', $dataPage);
    }

    public function pengajuan_cuti_view()
    {
        $dataPage = [
            'pageTitle' => "Cutie - Pengajuan Cuti",
            'page' => 'cutie-pengajuan-cuti',
        ];
        return view('pages.cuti-e.pengajuan-cuti', $dataPage);
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
}
