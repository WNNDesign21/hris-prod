<?php

namespace App\Http\Controllers\Lembure;

use Throwable;
use Carbon\Carbon;
use App\Models\Lembure;
use App\Models\Karyawan;
use App\Models\Organisasi;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\SettingLemburKaryawan;
use Illuminate\Support\Facades\Validator;

class LembureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Lembur-E - Dashboard",
            'page' => 'lembure-dashboard'
        ];
        return view('pages.lembur-e.index', $dataPage);
    }

    public function pengajuan_lembur_view()
    {
        $dataPage = [
            'pageTitle' => "Lembur-E - Pengajuan Lembur",
            'page' => 'lembure-pengajuan-lembur',
        ];
        return view('pages.lembur-e.pengajuan-lembur', $dataPage);
    }

    public function pengajuan_lembur_datatable(Request $request)
    {
        $columns = array(
            0 => 'lemburs.id_lembur',
            1 => 'lemburs.issued_date',
            2 => 'karyawans.nama',
            3 => 'lemburs.jenis_hari',
            4 => 'lemburs.total_durasi',
            5 => 'lemburs.status',
            6 => 'lemburs.plan_checked_by',
            7=> 'lemburs.plan_approved_by',
            8 => 'lemburs.plan_legalized_by',
            9 => 'lemburs.actual_checked_by',
            10 => 'lemburs.actual_approved_by',
            11 => 'lemburs.actual_legalized_by'
        );

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

        $totalData = Lembure::where('issued_by', auth()->user()->karyawan->id_karyawan)->count();
        $totalFiltered = $totalData;

        $lembure = Lembure::getData($dataFilter, $settings);
        $totalFiltered = $lembure->count();
        $dataTable = [];

        if (!empty($lembure)) {
            foreach ($lembure as $data) {
                $jam = floor($data->total_durasi / 60);
                $menit = $data->total_durasi % 60;

                $nestedData['id_lembur'] = $data->id_lembur;
                $nestedData['issued_date'] = Carbon::parse($data->issued_date)->locale('id')->translatedFormat('l, d F Y');
                $nestedData['issued_by'] = $data->nama_karyawan;
                $nestedData['jenis_hari'] = $data->jenis_hari;
                $nestedData['total_durasi'] = $jam . ' Jam ' . $menit . ' Menit';
                $nestedData['status'] = $data->status;
                $nestedData['plan_checked_by'] = $data->plan_checked_by;
                $nestedData['plan_approved_by'] = $data->plan_approved_by;
                $nestedData['plan_legalized_by'] = $data->plan_legalized_by;
                $nestedData['actual_checked_by'] = $data->actual_checked_by;
                $nestedData['actual_approved_by'] = $data->actual_approved_by;
                $nestedData['actual_legalized_by'] = $data->actual_legalized_by;
                $nestedData['aksi'] = '<div class="btn-group btn-group-sm">
                    <button type="button" class="waves-effect waves-light btn btn-sm btn-success btnDone" data-id-lembur="'.$data->id_lembur.'"><i class="far fa-check-circle"></i> Done</button>
                    <button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnEdit" data-id-lembur="'.$data->id_lembur.'"><i class="fas fa-edit"></i> Edit</button>
                    <button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnDelete" data-id-lembur="'.$data->id_lembur.'"><i class="fas fa-trash"></i> Delete</button>
                </div>';

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
            "column"=>$request->input('order.0.column')
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
            'jenis_hari' => ['required','in:WD,WE'],
            'karyawan_id.*' => ['required', 'distinct'],
            'job_description.*' => ['required'],
            'rencana_mulai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i'],
            'rencana_selesai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }
        
        $jenis_hari = $request->jenis_hari;
        $karyawan_ids = $request->karyawan_id;
        $job_descriptions = $request->job_description;
        $rencana_mulai_lemburs = $request->rencana_mulai_lembur;
        $rencana_selesai_lemburs = $request->rencana_selesai_lembur;
        $issued_by = auth()->user()->karyawan->id_karyawan;
        $organisasi_id = auth()->user()->organisasi_id;
        $departemen_id = auth()->user()->karyawan->posisi[0]->departemen_id;

        DB::beginTransaction();
        try {
            $header = Lembure::create([
                'id_lembur' => 'LEMBUR-' . '-' . Str::random(4).'-'. date('YmdHis'),
                'issued_by' => $issued_by,
                'organisasi_id' => $organisasi_id,
                'departemen_id' => $departemen_id,
                'jenis_hari' => $jenis_hari
            ]);


            //belum selesai
            $total_durasi = 0;
            $data_detail_lembur = [];
            foreach ($karyawan_ids as $key => $karyawan_id) {
                $karyawan = Karyawan::find($karyawan_id);
                $datetime_rencana_mulai_lembur = Carbon::createFromFormat('Y-m-d\TH:i', $rencana_mulai_lemburs[$key])->toDateTimeString();
                $datetime_rencana_selesai_lembur = Carbon::createFromFormat('Y-m-d\TH:i', $rencana_selesai_lemburs[$key])->toDateTimeString();
                $durasi = $this->calculate_overtime_per_minutes($datetime_rencana_mulai_lembur, $datetime_rencana_selesai_lembur);

                if($durasi < 60){
                    DB::rollback();
                    return response()->json(['message' => 'Durasi lembur '.$karyawan->nama.' kurang dari 1 jam, tidak perlu menginput SPL'], 402);
                }

                if(!$karyawan->posisi()->exists()){
                    DB::rollback();
                    return response()->json(['message' => $karyawan->nama.' belum memiliki posisi, Hubungi HRD untuk setting posisi karyawan!'], 402);
                }

                $nominal = $this->calculate_overtime_nominal($jenis_hari, $durasi, $karyawan_id);
                $data_detail_lembur[] = [
                    'karyawan_id' => $karyawan_id,
                    'organisasi_id' => $karyawan->user->organisasi_id,
                    'departemen_id' => $karyawan->posisi[0]->departemen_id,
                    'rencana_mulai_lembur' => $datetime_rencana_mulai_lembur,
                    'rencana_selesai_lembur' => $datetime_rencana_selesai_lembur,
                    'deskripsi_pekerjaan' => $job_descriptions[$key],
                    'durasi' => $durasi,
                    'nominal' => $nominal
                ];

                $total_durasi += $durasi;
            }
            
            $header->detailLembur()->createMany($data_detail_lembur);
            
            //Update Total Durasi Lagi
            $header->update(['total_durasi' => $total_durasi]);

            DB::commit();
            return response()->json(['message' => 'Lembur Berhasil Dibuat'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    //FUNGSI MENGHITUNG DURASI LEMBUR YANG SUDAH DIKURANGI DENGAN ISTIRAHAT
    public function calculate_overtime_per_minutes($datetime_start, $datetime_end)
    {
        //Kondisi Istirahat ketika lembur
        $start = Carbon::parse($datetime_start);
        $end = Carbon::parse($datetime_end);
        $duration = $start->diffInMinutes($end);

        // Setting Istirahat ketika lembur (Hari jumat memiliki perbedaan)
        if ($start->isFriday()) {
            $breaks = [
                ['start' => '11:30', 'end' => '13:00', 'duration' => 90],
                ['start' => '18:00', 'end' => '18:45', 'duration' => 45],
                ['start' => '02:30', 'end' => '03:15', 'duration' => 45],
            ];
        } else {
            $breaks = [
                ['start' => '12:00', 'end' => '12:45', 'duration' => 45],
                ['start' => '18:00', 'end' => '18:45', 'duration' => 45],
                ['start' => '02:30', 'end' => '03:15', 'duration' => 45],
            ];
        }

        // Adjust duration for each break period
        foreach ($breaks as $break) {
            $breakStart = Carbon::parse($start->format('Y-m-d') . ' ' . $break['start']);
            $breakEnd = Carbon::parse($start->format('Y-m-d') . ' ' . $break['end']);

            // If the break period spans over midnight, adjust the dates
            if ($breakStart->greaterThan($breakEnd)) {
                $breakEnd->addDay();
            }

            // Check if the overtime period overlaps with the break period
            if ($start->lessThanOrEqualTo($breakEnd) && $end->greaterThanOrEqualTo($breakStart)) {
                $duration -= $break['duration'];
            }
        }

        // memastikan bukan negatif
        $duration = max($duration, 0);

        // pembulatan durasi ke 0, 15, 30, 45
        // $remainder = $duration % 15;
        // if ($remainder != 0) {
        //     if ($remainder < 8) {
        //         $duration -= $remainder;
        //     } else {
        //         $duration += (15 - $remainder);
        //     }
        // }

        $duration = intval($duration);
        return $duration;
    }

    //FUNGSI MENGHITUNG NOMINAL LEMBUR
    public function calculate_overtime_nominal($jenis_hari, $durasi, $karyawan_id)
    {
        $setting_lembur_karyawan = SettingLemburKaryawan::where('karyawan_id', $karyawan_id)->first();
        $karyawan = Karyawan::find($karyawan_id);
        $convert_duration = number_format($durasi / 60, 1);
        $gaji_lembur_karyawan = $setting_lembur_karyawan->gaji;
        $jabatan_id = $karyawan->posisi[0]->jabatan_id;
        $upah_sejam = $gaji_lembur_karyawan / 173;

        //PERHITUNGAN SESUAI JENIS HARI
        if($jenis_hari == 'WD'){

            //PERHITUNGAN UNTUK LEADER DAN STAFF
            if($jabatan_id >= 5){
                $jam_pertama = 1 * $upah_sejam * 1.5; 
                $jam_kedua = $convert_duration > 1 ? ($convert_duration - 1) * $upah_sejam * 2 : 0;
                $nominal_lembur = $jam_pertama + $jam_kedua;
            
            //PERHITUNGAN UNTUK JABATAN LAINNYA
            } else {
                $nominal_lembur = 0;
            }

        } else {
            //PERHITUNGAN UNTUK LEADER DAN STAFF
            if($jabatan_id >= 5){
                $delapan_jam_pertama = $convert_duration <= 8 ? ($convert_duration * $upah_sejam * 2) : (8 * $upah_sejam * 2);
                $jam_ke_sembilan = $convert_duration > 8 && $convert_duration <= 9 ? (($convert_duration - 8) * $upah_sejam * 3) : ($convert_duration > 9 ? $upah_sejam * 3 : 0);
                $jam_ke_sepuluh = $convert_duration > 9 ? ($convert_duration - 9) * $upah_sejam * 4 : 0;
                $nominal_lembur = $delapan_jam_pertama + $jam_ke_sembilan + $jam_ke_sepuluh;


            //PERHITUNGAN UNTUK SECTION HEAD
            } elseif ($jabatan_id == 4){
                if ($convert_duration >= 4){
                    $nominal_lembur = 250000;
                } elseif ($convert_duration >= 3){
                    $nominal_lembur = 115000;
                } elseif ($convert_duration >= 2){
                    $nominal_lembur = 65000;
                } elseif ($convert_duration >= 1){
                    $nominal_lembur = 32500;
                } else {
                    $nominal_lembur = 0;
                }

            //PERHITUNGAN UNTUK DEPARTEMEN HEAD
            } elseif ($jabatan_id == 3) {
                if ($convert_duration >= 4){
                    $nominal_lembur = 400000;
                } elseif ($convert_duration >= 3){
                    $nominal_lembur = 115000;
                } elseif ($convert_duration >= 2){
                    $nominal_lembur = 65000;
                } elseif ($convert_duration >= 1){
                    $nominal_lembur = 32500;
                } else {
                    $nominal_lembur = 0;
                }
            
            //PERHITUNGAN UNTUK PLANT HEAD
            } else {
                $nominal_lembur = 0;
            }
        }

        return intval($nominal_lembur);

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
    public function update(Request $request, string $id_lembur)
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


    //GET DATA KARYAWAN MEMBER DARI LEADER
    public function get_data_karyawan_lembur(Request $request){
        $search = $request->input('search');
        $page = $request->input("page");
        $idCats = $request->input('catsProd');
        $adOrg = $request->input('adOrg');

        $query = Karyawan::select(
            'karyawans.id_karyawan',
            'karyawans.nama',
            'posisis.nama as posisi',
        );

        $posisi = auth()->user()->karyawan->posisi;
        $id_posisi_members = $this->get_member_posisi($posisi);

        foreach ($posisi as $ps){
            $index = array_search($ps->id_posisi, $id_posisi_members);
            array_splice($id_posisi_members, $index, 1);
        }

        if (!empty($search)) {
            $query->where(function ($dat) use ($search) {
                $dat->where('id_karyawan', 'ILIKE', "%{$search}%")
                    ->orWhere('nama', 'ILIKE', "%{$search}%");
            });
        }

        //Ambil karyawan yang scope Aktif jika ada parameter status
        $query->aktif();
        $query->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
        ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
        ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')
        ->rightJoin('setting_lembur_karyawans', 'karyawans.id_karyawan', 'setting_lembur_karyawans.karyawan_id');

        $query->whereIn('posisis.id_posisi', $id_posisi_members);
        $query->orWhere('karyawans.id_karyawan', auth()->user()->karyawan->id_karyawan);

        $query->groupBy('karyawans.id_karyawan','karyawans.nama', 'posisis.nama',);

        $data = $query->simplePaginate(10);

        $morePages = true;
        $pagination_obj = json_encode($data);
        if (empty($data->nextPageUrl())) {
            $morePages = false;
        }

        foreach ($data->items() as $karyawan) {
            $dataUser[] = [
                'id' => $karyawan->id_karyawan,
                'text' => $karyawan->nama
            ];
        }

        $results = array(
            "results" => $dataUser,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }

    public function get_karyawan_lembur()
    {
        $query = Karyawan::select(
            'karyawans.id_karyawan',
            'karyawans.nama',
            'posisis.nama as posisi',
        );

        $posisi = auth()->user()->karyawan->posisi;
        $id_posisi_members = $this->get_member_posisi($posisi);

        foreach ($posisi as $ps){
            $index = array_search($ps->id_posisi, $id_posisi_members);
            array_splice($id_posisi_members, $index, 1);
        }

        //Ambil karyawan yang scope Aktif jika ada parameter status
        $query->aktif();
        $query->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
        ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
        ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')
        ->rightJoin('setting_lembur_karyawans', 'karyawans.id_karyawan', 'setting_lembur_karyawans.karyawan_id');

        $query->whereIn('posisis.id_posisi', $id_posisi_members);
        $query->orWhere('karyawans.id_karyawan', auth()->user()->karyawan->id_karyawan);

        $query->groupBy('karyawans.id_karyawan','karyawans.nama', 'posisis.nama',);
        $data = $query->get();

        $karyawanLembur = [];
        if($data){
            foreach ($data as $karyawan) {
                $karyawanLembur[] = [
                    'id' => $karyawan->id_karyawan,
                    'text' => $karyawan->nama
                ];
            }
        };

        return response()->json(['message' => 'Data Karyawan Berhasil Ditemukan', 'data' => $karyawanLembur], 200);

        
    }

    function get_member_posisi($posisis)
    {
        $data = [];
        foreach ($posisis as $ps) {
            if ($ps->children) {
                $data = array_merge($data, $this->get_member_posisi($ps->children));
            }
            $data[] = $ps->id_posisi;
        }
        return $data;
    }

    public function get_data_lembur(string $id_lembur)
    {
        try {
            $lembur = Lembure::findOrFail($id_lembur);
            $data_detail_lembur = [];
            foreach ($lembur->detailLembur as $data){
                $data_detail_lembur[] = [
                    'id_detail_lembur' => $data->id_detail_lembur,
                    'lembur_id' => $data->lembur_id,
                    'karyawan_id' => $data->karyawan_id,
                    'organisasi_id' => $data->organisasi_id,
                    'departemen_id' => $data->departemen_id,
                    'divisi_id' => $data->divisi_id,
                    'rencana_mulai_lembur' => $data->rencana_mulai_lembur ? Carbon::parse($data->rencana_mulai_lembur)->format('Y-m-d\TH:i') : null,
                    'rencana_selesai_lembur' => $data->rencana_selesai_lembur ? Carbon::parse($data->rencana_selesai_lembur)->format('Y-m-d\TH:i') : null,
                    'aktual_mulai_lembur' => $data->aktual_mulai_lembur ? Carbon::parse($data->aktual_mulai_lembur)->format('Y-m-d\TH:i') : null,
                    'aktual_selesai_lembur' => $data->aktual_selesai_lembur ? Carbon::parse($data->aktual_selesai_lembur)->format('Y-m-d\TH:i') : null,
                    'is_rencana_approved' => $data->is_rencana_approved,
                    'is_aktual_approved' => $data->is_aktual_approved,
                    'deskripsi_pekerjaan' => $data->deskripsi_pekerjaan
                ];
            }
            $data = [
                'header' => $lembur,
                'detail_lembur' => $data_detail_lembur
            ];
            return response()->json(['message' => 'Berhasil mendapatkan data lembur', 'data' => $data], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Data lembur tidak tersedia, hubungi ICT!', 'data' => []], 500);
        }
    }
}
