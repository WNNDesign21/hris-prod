<?php

namespace App\Http\Controllers\MasterData;

use Throwable;
use Carbon\Carbon;
use App\Models\Posisi;
use App\Models\Kontrak;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class KontrakController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Master Data - Kontrak",
            'page' => 'masterdata-kontrak',
        ];
        return view('pages.master-data.kontrak.index', $dataPage);
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
        
    }

    public function store_or_update(Request $request)
    {
        $dataValidate = [
            'karyawan_id_kontrakEdit' => ['required'],
            'jenis_kontrakEdit' => ['required'],
            'posisi_kontrakEdit' => ['required'],
            'durasi_kontrakEdit' => ['numeric','nullable'],
            'salary_kontrakEdit' => ['numeric','required'],
            'tanggal_mulai_kontrakEdit' => ['required', 'date'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $karyawan_id = $request->karyawan_id_kontrakEdit;
        $jenis = $request->jenis_kontrakEdit;
        $posisi_id = $request->posisi_kontrakEdit;
        $durasi = $request->durasi_kontrakEdit;
        $salary = $request->salary_kontrakEdit;
        $deskripsi = $request->deskripsi_kontrakEdit;
        $tanggal_mulai = Carbon::parse($request->tanggal_mulai_kontrakEdit);
        $id_kontrak = $request->id_kontrakEdit;

        if($durasi){
            $tanggal_selesai = $tanggal_mulai->addMonths($durasi)->toDateString();
        } else {
            $tanggal_selesai = null;
        }

        DB::beginTransaction();
        try{

            if(!id_kontrak){
                if($jenis !== 'PKWTT'){
                    $kontrak = Kontrak::create([
                        'id_kontrak' => 'KONTRAK-'. Str::random(4) . '-' . now()->timestamp,
                        'karyawan_id' => $karyawan_id,
                        'posisi_id' => $posisi_id,
                        'nama_posisi' => Posisi::find($posisi_id)->nama_posisi,
                        'jenis' => $jenis,
                        'durasi' => $durasi,
                        'salary' => $salary,
                        'deskripsi' => $deskripsi,
                        'tanggal_mulai' => $tanggal_mulai,
                        'tanggal_selesai' => $tanggal_selesai,
                    ]);
                } else {
                    $kontrak = Kontrak::create([
                        'id_kontrak' => 'KONTRAK-'. Str::random(4) . '-' . now()->timestamp,
                        'karyawan_id' => $karyawan_id,
                        'posisi_id' => $posisi_id,
                        'nama_posisi' => Posisi::find($posisi_id)->nama_posisi,
                        'jenis' => $jenis,
                        'durasi' => $durasi,
                        'salary' => $salary,
                        'deskripsi' => $deskripsi,
                        'tanggal_mulai' => $tanggal_mulai,
                    ]);
                }
                $text = 'Kontrak Berhasil Ditambahkan!';
            } else {
                $kontrak = Kontrak::find($id_kontrak);
                $kontrak->update([
                    'posisi_id' => $posisi_id,
                    'nama_posisi' => Posisi::find($posisi_id)->nama_posisi,
                    'jenis' => $jenis,
                    'durasi' => $durasi,
                    'salary' => $salary,
                    'deskripsi' => $deskripsi,
                    'tanggal_mulai' => $tanggal_mulai,
                    'tanggal_selesai' => $jenis !== 'PKWTT' ? $tanggal_selesai : null,
                ]);
                $text = 'Kontrak Berhasil Diupdate!';
            }
            DB::commit();
            return response()->json(['message' => $text],200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Database error: ' . $e->getMessage()], 500);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Model not found: ' . $e->getMessage()], 404);
        }
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
