<?php

namespace App\Http\Controllers\MasterData;

use Throwable;
use Carbon\Carbon;
use App\Models\Event;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Master Data - Kalender Perusahaan",
            'page' => 'masterdata-event',
        ];
        return view('pages.master-data.event.index', $dataPage);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'jenis_event',
            1 => 'keterangan',
            2 => 'durasi',
            3 => 'tanggal_mulai',
            4 => 'tanggal_selesai'
        );

        $totalData = Event::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = (!empty($request->input('order.0.column'))) ? $columns[$request->input('order.0.column')] : $columns[0];
        $dir = (!empty($request->input('order.0.dir'))) ? $request->input('order.0.dir') : "DESC";

        $settings['start'] = $start;
        $settings['limit'] = $limit;
        $settings['dir'] = $dir;
        $settings['order'] = $order;

        $dataFilter = [];
        $search = $request->input('search.value');
        if (!empty($search)) {
            $dataFilter['search'] = $search;
        }

        $event = Event::getData($dataFilter, $settings);
        $totalFiltered = Event::countData($dataFilter);

        $dataTable = [];

        if (!empty($event)) {
            foreach ($event as $data) {
                $nestedData['jenis_event'] = $data->jenis_event == 'EP' ? 'Event Perusahaan' : 'Cuti Bersama';
                $nestedData['keterangan'] = $data->keterangan;
                $nestedData['durasi'] = $data->durasi.' Hari';
                $nestedData['tanggal_mulai'] = Carbon::parse($data->tanggal_mulai)->format('d-m-Y');
                $nestedData['tanggal_selesai'] = Carbon::parse($data->tanggal_selesai)->format('d-m-Y');
                $nestedData['aksi'] = '';
                if (Carbon::parse($data->tanggal_mulai)->year == Carbon::now()->year) {
                    $nestedData['aksi'] = '
                    <div class="btn-group">
                        <button type="button" class="waves-effect waves-light btn btn-danger btnDelete" data-id="'.$data->id_event.'"><i class="fas fa-trash-alt"></i></button>
                    </div>
                    ';
                } else {
                    $nestedData['aksi'] = 'Cannot delete event from previous year';
                }

                // <button type="button" class="waves-effect waves-light btn btn-warning btnEdit" data-id="'.$data->id_event.'" data-jenis-event="'.$data->jenis_event.'" data-keterangan="'.$data->keterangan.'" data-tanggal-mulai="'.$data->tanggal_mulai.'" data-tanggal-selesai="'.$data->tanggal_selesai.'"><i class="fas fa-edit"></i></button>
                $dataTable[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $dataTable,
            "order" => $order,
            "statusFilter" => !empty($dataFilter['statusFilter']) ? $dataFilter['statusFilter'] : "Kosong",
            "dir" => $dir,
        );

        return response()->json($json_data, 200);
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
        $dataValidate = [
            'jenis_event' => ['required'],
            'keterangan' => ['required'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date'],
        ];
    
        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $jenis_event = $request->jenis_event;
        $keterangan = $request->keterangan;
        $tanggal_mulai = $request->tanggal_mulai;
        $tanggal_selesai = $request->tanggal_selesai;
        $durasi = Carbon::parse($tanggal_mulai)->diffInDays($tanggal_selesai) + 1;
        $organisasi_id = auth()->user()->organisasi_id;
    
        DB::beginTransaction();
        try{

            if($jenis_event == 'CB'){
                // GET KARYAWAN YANG TANGGAL SELESAI NYA LEBIH BESAR DARI TANGGAL SELESAI CUTI BERSAMA (KARYAWAN MENGIKUTI CUTI BERSAMA)
                $karyawans = Karyawan::aktif()->organisasi($organisasi_id)->where('tanggal_selesai', '>=', $tanggal_mulai)->orWhere('jenis_kontrak', 'PKWTT')->get();

                // KURANGI SISA CUTI BERSAMA SETIAP KARYAWAN YANG MENGIKUTI CUTI BERSAMA
                foreach($karyawans as $kry){
                    $sisa_cuti_bersama_after = $kry->sisa_cuti_bersama - $durasi;
                    if($sisa_cuti_bersama_after < 0){
                        $kry->update([
                            'sisa_cuti_bersama' => 0,
                            'hutang_cuti' => abs($sisa_cuti_bersama_after) + $kry->hutang_cuti
                        ]);
                    } else {
                        $kry->update([
                            'sisa_cuti_bersama' => $sisa_cuti_bersama_after
                        ]); 
                    }
                }
            }

            $event = Event::create([
                'jenis_event' => $jenis_event,
                'keterangan' => $keterangan,
                'durasi' => $durasi,
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai,
                'organisasi_id' => $organisasi_id
            ]);

            DB::commit();
            return response()->json(['message' => 'Event Ditambahkan!'],200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
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

    public function delete(string $id_event)
    {
        $organisasi_id = auth()->user()->organisasi_id;
        DB::beginTransaction();
        try {
            $event = Event::findOrFail($id_event); 
            if($event->jenis_event == 'CB'){
                $karyawans = Karyawan::aktif()->organisasi($organisasi_id)->where('tanggal_selesai', '>=', $event->tanggal_mulai)->orWhere('jenis_kontrak', 'PKWTT')->get();
                if($karyawans){
                    foreach($karyawans as $kry){
                        $sisa_cuti_bersama_after = $kry->sisa_cuti_bersama + $event->durasi;
                        if($kry->hutang_cuti > 0){
                            $sisa_cuti_bersama_after = $sisa_cuti_bersama_after - $kry->hutang_cuti;
                            if($sisa_cuti_bersama_after < 0){
                                Karyawan::find($kry->id_karyawan)->update([
                                    'sisa_cuti_bersama' => 0,
                                    'hutang_cuti' => abs($sisa_cuti_bersama_after)
                                ]);
                            } else {
                                Karyawan::find($kry->id_karyawan)->update([
                                    'sisa_cuti_bersama' => $sisa_cuti_bersama_after,
                                    'hutang_cuti' => 0
                                ]); 
                            }
                        } else {
                            Karyawan::find($kry->id_karyawan)->update([
                                'sisa_cuti_bersama' => $sisa_cuti_bersama_after
                            ]); 
                        }
                    }
                }
            }
            $event->delete();
            DB::commit();
            return response()->json(['message' => 'Event deleted!'], 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Throwable $e) {
            DB::rollback();
            Log::error('Error deleting event: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_data_event_calendar(){
        $organisasi_id = auth()->user()->organisasi_id;
        $event = Event::organisasi($organisasi_id)->get();
        $data = [];

        if($event){
            foreach ($event as $e) {
                if($e->jenis_event == 'CB'){
                    $className = 'bg-primary';
                } else {
                    $className = 'bg-info';
                }
                $data[] = [
                    'title' => $e->keterangan,
                    'start' => $e->tanggal_mulai,
                    'end' => $e->tanggal_selesai !== $e->tanggal_mulai ? Carbon::parse($e->tanggal_selesai)->addDay()->format('Y-m-d') : $e->tanggal_selesai,
                    'className' => $className,
                    'nama_karyawan' => 'Seluruh Karyawan',
                    'karyawan_pengganti' => '-',
                    'jenis_cuti' => $e->jenis_event == 'CB' ? 'CUTI BERSAMA' : 'EVENT PERUSAHAAN',
                    'rencana_mulai_cuti' => Carbon::parse($e->tanggal_mulai)->format('d M Y'),
                    'rencana_selesai_cuti' => Carbon::parse($e->tanggal_selesai)->format('d M Y'),
                    'alasan_cuti' => $e->keterangan,
                    'durasi_cuti' => $e->durasi.' Hari',
                    'status_cuti' => $e->tanggal_mulai > now() ? 'SCHEDULED' : 'COMPLETED',
                    'attachment' => 'No Attachment Needed',
                    'aktual_mulai_cuti' => $e->tanggal_mulai > now() ? '-' : Carbon::parse($e->tanggal_mulai)->format('d M Y'),
                    'aktual_selesai_cuti' => $e->tanggal_selesai > now() ? '-' : Carbon::parse($e->tanggal_selesai)->format('d M Y'),
                ];
            }
        }
        
        return response()->json($data, 200);
    }
}
