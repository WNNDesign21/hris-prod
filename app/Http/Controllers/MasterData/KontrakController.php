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
        $durasi = (int)$request->durasi_kontrakEdit;
        $salary = $request->salary_kontrakEdit;
        $deskripsi = $request->deskripsi_kontrakEdit;
        $tanggal_mulai = $request->tanggal_mulai_kontrakEdit;
        $id_kontrak = $request->id_kontrakEdit;


        if($durasi !== 0){
            $tanggal_selesai = Carbon::parse($request->tanggal_mulai_kontrakEdit)->addMonths($durasi)->toDateString();
        } else {
            $tanggal_selesai = null;
        }

        DB::beginTransaction();
        try{

            if(!$id_kontrak){
                if($jenis !== 'PKWTT'){

                    if ($durasi === 0) {
                        DB::commit();
                        return response()->json(['message' => 'Durasi tidak boleh kosong!'], 402);
                    }

                    $kontrak = Kontrak::create([
                        'id_kontrak' => 'KONTRAK-'. Str::random(4) . '-' . now()->timestamp,
                        'karyawan_id' => $karyawan_id,
                        'posisi_id' => $posisi_id,
                        'nama_posisi' => Posisi::find($posisi_id)->nama,
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
                        'nama_posisi' => Posisi::find($posisi_id)->nama,
                        'jenis' => $jenis,
                        'durasi' => $durasi,
                        'salary' => $salary,
                        'deskripsi' => $deskripsi,
                        'tanggal_mulai' => $tanggal_mulai,
                    ]);
                }
                $text = 'Kontrak Berhasil Ditambahkan!';
            } else {

                if ($durasi === 0 && $jenis !== 'PKWTT') {
                    DB::commit();
                    return response()->json(['message' => 'Durasi tidak boleh kosong!'], 402);
                }

                $kontrak = Kontrak::find($id_kontrak);
                $kontrak->update([
                    'posisi_id' => $posisi_id,
                    'nama_posisi' => Posisi::find($posisi_id)->nama,
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
            return response()->json(['message' => $text, 'data' => $kontrak],200);
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

    public function get_data_list_kontrak(string $karyawan_id)
    {
        $kontrak = Kontrak::where('karyawan_id', $karyawan_id)->orderBy('tanggal_mulai', 'DESC')->get();
        $list = [];
        if($kontrak){
            foreach($kontrak as $item){
                $list[] = [
                    'id_kontrak' => $item->id_kontrak,
                    'nama_posisi' => $item->nama_posisi,
                    'posisi_id' => $item->posisi_id,
                    'jenis' => $item->jenis,
                    'status' => $item->status,
                    'durasi' => $item->durasi,
                    'salary' => 'Rp. ' . number_format($item->salary, 0, ',', '.'),
                    'deskripsi' => $item->deskripsi,
                    'tanggal_mulai' => Carbon::parse($item->tanggal_mulai)->format('d M Y'),
                    'tanggal_selesai' => Carbon::parse($item->tanggal_selesai)->format('d M Y') ? Carbon::parse($item->tanggal_selesai)->format('d M Y') : '-',
                ];
            }
            return response()->json(['data' => $list], 200);
        } else {
            return response()->json(['message' => 'Data Karyawan tidak ditemukan!'], 404);
        }

    }
}
