<?php

namespace App\Http\Controllers\TugasLuare;

use Throwable;
use Carbon\Carbon;
use App\Models\Karyawan;
use App\Helpers\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TugasLuare\TugasLuar;
use Illuminate\Support\Facades\Validator;

class ApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "TugasLuar-E - Approval TL",
            'page' => 'tugasluare-approval',
        ];
        return view('pages.tugasluar-e.approval.index', $dataPage);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'tugasluars.id_tugasluar',
            1 => 'tugasluars.karyawan_id',
            2 => 'tugasluars.tanggal',
            3 => 'tugasluars.jenis_kendaraan',
            4 => 'tugasluars.tanggal_pergi_planning',
            5 => 'tugasluars.tanggal_kembali_planning',
            6 => 'tugasluars.km_awal',
            7 => 'tugasluars.km_akhir',
            8 => 'tugasluars.km_selisih',
            9 => 'tugasluars.tempat_asal',
            10 => 'tugasluars.keterangan',
            11 => 'tugasluars.checked_at',
            12 => 'tugasluars.legalized_at',
            13 => 'tugasluars.status',
        );

        $totalData = TugasLuar::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $settings['start'] = $start;
        $settings['limit'] = $limit;
        $settings['dir'] = $dir;
        $settings['order'] = $order;

        $dataFilter = [];

        $is_can_checked = false;
        $is_can_legalized = false;
        $is_can_known = false;
        $organisasi_id = auth()->user()->organisasi_id;

        // FILTER
        if(auth()->user()->hasRole('personalia')){
            $dataFilter['organisasi_id'] = $organisasi_id;
            $is_can_legalized = true;
        }

        if(auth()->user()->hasRole('security')){
            $dataFilter['organisasi_id'] = $organisasi_id;
            $is_can_known = true;
        } 

        if (auth()->user()->hasRole('atasan')){
            $posisi = auth()->user()->karyawan->posisi;
            $id_posisi_members = Approval::GetMemberPosisi($posisi);

            foreach ($posisi as $ps){
                $index = array_search($ps->id_posisi, $id_posisi_members);
                array_splice($id_posisi_members, $index, 1);
            }

            if (auth()->user()->karyawan->posisi[0]->jabatan_id <= 5){
                $is_can_checked = true;
            } 

            $dataFilter['member_posisi_id'] = $id_posisi_members;
        } 
        
        $search = $request->input('search.value');
        if (!empty($search)) {
            $dataFilter['search'] = $search;
        }

        $filterNopol = $request->nopol;
        if(isset($filterNopol)){
            $dataFilter['nopol'] = $filterNopol;
        }

        $filterStatus = $request->status;
        if(isset($filterStatus)){
            $dataFilter['status'] = $filterStatus;
        }

        $filterFrom = $request->from;
        if(isset($filterFrom)){
            $dataFilter['from'] = $filterFrom;
        }

        $filterTo = $request->to;
        if(isset($filterTo)){
            $dataFilter['to'] = $filterTo;
        }

        $tugasluars = TugasLuar::getData($dataFilter, $settings);
        $totalFiltered = TugasLuar::countData($dataFilter);

        $dataTable = [];

        if (!empty($tugasluars)) {
            foreach ($tugasluars as $data) {
                $karyawan = Karyawan::find($data->karyawan_id);
                $posisi = $karyawan->posisi;
                $has_leader = Approval::HasLeader($posisi);
                $has_section_head = Approval::HasSectionHead($posisi);
                $has_department_head = Approval::HasDepartmentHead($posisi);

                $checked = '';
                $legalized = '';
                $known = '';
                $rejected = '';
                $aksi = '-';
                $is_rejected = false;
                $formattedPengikut = '';
                if ($data->pengikut()->exists()) {
                    $pengikuts = $data->pengikut()->pluck('karyawan_id')->toArray();
                    $pengikutNames = Karyawan::whereIn('id_karyawan', $pengikuts)->pluck('nama')->toArray();
                    $formattedPengikut = array_map(function($pengikut) {
                        return '<span class="badge badge-primary m-1">' . $pengikut . '</span>';
                    }, $pengikutNames);
                }

                if($data->no_polisi) {
                    $no_polisi_array = explode('-', $data->no_polisi);
                    $kode_wilayah = $no_polisi_array[0];
                    $nomor_polisi = $no_polisi_array[1];
                    $seri_akhir = $no_polisi_array[2];
                }

                $jenis_kepemilikan = $data->jenis_kepemilikan == 'OP' ? 'OPERASIONAL' : ($data->jenis_kepemilikan == 'OJ' ? 'OPERASIONAL JABATAN' : 'PRIBADI');
                $jenis_kendaraan = $data->jenis_kendaraan == 'MOTOR' ? '🏍️' : '🚗';
                $kendaraan = '<small class="text-center">'.$jenis_kendaraan.' '.$data?->no_polisi.'<br><span class="text-center">'.$jenis_kepemilikan.'</span></small>';
                $jenis_keberangkatan_text = $data->jenis_keberangkatan == 'RMH' ? 'RUMAH' : ($data->jenis_keberangkatan == 'KTR' ? 'KANTOR' : 'LAINNYA');
                $rute = '<div class="d-flex gap-1 text-center">'.'<p><small class="text-fade">'.strtoupper($data->tempat_asal).'</small></p>'.' ➡️ '.'<p><small class="text-fade">'.strtoupper($data->tempat_tujuan).'</small></p>'.'</div><div class="d-flex justify-content-center"><p><small> Driver : '.$data->nama_pengemudi.'</small><br><small> From : '.$jenis_keberangkatan_text.'</small></p></div>';
                $status = $data->status == 'WAITING' ? '<span class="badge badge-warning">WAITING</span>' : ($data->status == 'ONGOING' ? '<span class="badge badge-info">ON GOING</span>' : ($data->status == 'COMPLETED' ? '<span class="badge badge-success">COMPLETED</span>' : '<span class="badge badge-danger">REJECTED</span>'));
                $jam_pergi = '<div class="d-flex gap-1 text-center">
                                <p>' . Carbon::createFromFormat('Y-m-d H:i:s', $data->tanggal_pergi_planning)->format('H:i') . ' WIB <span class="badge badge-warning">Planning</span></p>
                                <br>
                                <p>' . ($data->tanggal_pergi_aktual ? Carbon::createFromFormat('Y-m-d H:i:s', $data->tanggal_pergi_aktual)->format('H:i') . ' WIB <span class="badge badge-success">Aktual</span>' : '') . '</p>
                              </div>';
                $jam_kembali = $data->tanggal_kembali_planning ? '<div class="d-flex gap-1 text-center">
                                <p>' . Carbon::createFromFormat('Y-m-d H:i:s', $data->tanggal_kembali_planning)->format('H:i') . ' WIB <span class="badge badge-warning">Planning</span></p>
                                <br>
                                <p>' . ($data->tanggal_kembali_aktual ? Carbon::createFromFormat('Y-m-d H:i:s', $data->tanggal_kembali_aktual)->format('H:i') . ' WIB <span class="badge badge-success">Aktual</span>' : '') . '</p>
                            </div>' : '-';

                if($data->checked_by) {
                    $checked = '✅<br><small class="text-bold">'.$data?->checked_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked_at)->diffForHumans().'</small>';
                }

                if($data->legalized_by) {
                    $legalized = '✅<br><small class="text-bold">'.$data?->legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->legalized_at)->diffForHumans().'</small>';
                }

                if($data->known_by) {
                    $known = '✅<br><small class="text-bold">'.$data?->known_by.'</small><br><small class="text-fade">'.Carbon::parse($data->known_at)->diffForHumans().'</small>';
                }

                // APPROVAL
                if($is_can_checked) {
                    if(!$data->checked_by){
                        if(auth()->user()->karyawan->posisi[0]->jabatan_id == 5) {
                            if (!$has_section_head && !$has_department_head) {
                                $checked = '<div class="btn-group"><button class="btn btn-sm btn-success btnChecked" data-id-tugasluar="'.$data->id_tugasluar.'">Checked</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-tugasluar="'.$data->id_tugasluar.'">Reject</button></div>';
                            }
                        } else {
                            $checked = '<div class="btn-group"><button class="btn btn-sm btn-success btnChecked" data-id-tugasluar="'.$data->id_tugasluar.'">Checked</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-tugasluar="'.$data->id_tugasluar.'">Reject</button></div>';
                        }
                    }
                }

                if($is_can_legalized) {
                    if(!$data->legalized_by){
                        $legalized = '<div class="btn-group"><button class="btn btn-sm btn-success btnLegalized" data-id-tugasluar="'.$data->id_tugasluar.'"  data-jenis-keberangkatan="'.$data->jenis_keberangkatan.'">Legalized</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-tugasluar="'.$data->id_tugasluar.'">Reject</button></div>';
                    }
                }

                if($data->rejected_by) {
                    $is_rejected = true;
                    $rejected = '❌<br><small class="text-bold">'.$data?->rejected_by.'</small><br><small class="text-fade">'.Carbon::parse($data->rejected_at)->diffForHumans().'</small>';
                }

                $nestedData['id_tugasluar'] = $data->id_tugasluar;
                $nestedData['karyawan'] = $formattedPengikut;
                $nestedData['tanggal'] = Carbon::parse($data->tanggal)->format('d M Y');
                $nestedData['kendaraan'] = $kendaraan;
                $nestedData['pergi'] = $jam_pergi;
                $nestedData['kembali'] = $jam_kembali;
                $nestedData['km_awal'] = $data->km_awal . ' Km';
                $nestedData['km_akhir'] = $data->km_akhir . ' Km';
                $nestedData['km_selisih'] = $data->km_selisih . ' Km';
                $nestedData['rute'] = $rute;
                $nestedData['keterangan'] = $data->keterangan;
                $nestedData['status'] = $status;
                $nestedData['checked'] = $is_rejected ? $rejected : $checked;
                $nestedData['legalized'] = $is_rejected ? $rejected : $legalized;
                $nestedData['aksi'] = $aksi;

                $dataTable[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $dataTable,
            "order" => $order,
            "dir" => $dir,
        );

        return response()->json($json_data, 200);
    }

    public function checked(Request $request, string $id_tugasluar)
    {
        $tugasluar = TugasLuar::find($id_tugasluar);

        DB::beginTransaction();
        try{
            if ($tugasluar->checked_by) {
                return response()->json(['message' => 'Pengajuan TL sudah di checked, silahkan refresh halaman!'], 403);
            } elseif ($tugasluar->rejected_by) {
                return response()->json(['message' => 'Pengajuan TL yang sudah di reject tidak dapat di Checked!'], 403);
            }

            $tugasluar->checked_by = auth()->user()->karyawan->nama;
            $tugasluar->checked_at = now();
            $tugasluar->save();

            DB::commit();
            return response()->json(['message' => 'TL berhasil di Checked!'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function legalized(Request $request, string $id_tugasluar)
    {
        $tugasluar = TugasLuar::find($id_tugasluar);

        DB::beginTransaction();
        try{
            if ($tugasluar->legalized_by) {
                return response()->json(['message' => 'Pengajuan TL sudah di Legalized, silahkan refresh halaman!'], 403);
            } elseif ($tugasluar->rejected_by) {
                return response()->json(['message' => 'Pengajuan TL yang sudah di reject tidak dapat di Checked!'], 403);
            }

            $tugasluar->legalized_by = 'HRD & GA';
            $tugasluar->legalized_at = now();
            $tugasluar->save();

            DB::commit();
            return response()->json(['message' => 'TL berhasil di Legalized!'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function rejected(Request $request, string $id_tugasluar)
    {
        $dataValidate = [
            'rejected_note' => ['required'],
        ];
        
        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }
        $tugasluar = TugasLuar::find($id_tugasluar);

        DB::beginTransaction();
        try{
            if ($tugasluar->rejected_by) {
                return response()->json(['message' => 'Pengajuan Tugas Luar sudah di Reject, Refresh halaman ini!'], 403);
            }

            if(auth()->user()->hasRole('personalia')){
                $tugasluar->rejected_by = 'HRD & GA';
            } elseif (auth()->user()->hasRole('security')){ 
                $tugasluar->rejected_by = 'SECURITY';
            } else {
                $tugasluar->rejected_by = auth()->user()->karyawan->nama;
            }

            $tugasluar->rejected_at = now();
            $tugasluar->rejected_note = $request->rejected_note;
            $tugasluar->save();

            DB::commit();
            return response()->json(['message' => 'Tugas Luar berhasil di Reject!'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
