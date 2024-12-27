<?php

namespace App\Http\Controllers\Attendancee;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AttendanceeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Attendance-E - Dashboard",
            'page' => 'attendancee-dashboard',
        ];
        return view('pages.attendance-e.index', $dataPage);
    }

    /**
     * Display a listing of the resource.
     */
    public function scanlog_view()
    {
        $dataPage = [
            'pageTitle' => "Attendance-E - Scanlog",
            'page' => 'attendancee-scanlog',
        ];
        return view('pages.attendance-e.scanlog', $dataPage);
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
