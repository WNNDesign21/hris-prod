<?php

namespace App\Http\Controllers\KSK;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\KSK\DetailKSK;
use App\Http\Controllers\Controller;

class TindakLanjutController extends Controller
{
    public function index()
    {
        // return redirect()->route('under-maintenance');
        $dataPage = [
            'pageTitle' => "KSK-E - Tindak Lanjut KSK",
            'page' => 'ksk-tindak-lanjut',
        ];
        return view('pages.ksk-e.tindak-lanjut.index', $dataPage);
    }

    public function datatable_need_action(Request $request)
    {
        $columns = array(
            0 => 'ksk_details.cleareance_id',
            1 => 'karyawans.nama',
            2 => 'ksk_details.nama_departemen',
            3 => 'ksk_details.nama_jabatan',
            4 => 'ksk_details.nama_posisi',
            5 => 'ksk_details.tanggal_akhir_bekerja',
            6 => 'ksk_details.status_ksk',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $settings['start'] = $start;
        $settings['limit'] = $limit;
        $settings['dir'] = $dir;
        $settings['order'] = $order;

        $dataFilter = [];
        $search = $request->input('search.value');
        if (!empty($search)) {
            $dataFilter['search'] = $search;
        }

        $dataFilter['module'] = 'need_action';
        $detailKSK = DetailKSK::getData($dataFilter, $settings);
        $totalData = DetailKSK::countData($dataFilter);
        $totalFiltered = $totalData;

        $dataTable = [];

        if (!empty($detailKSK)) {
            foreach ($detailKSK as $data) {
                $idKSK = '<a href="javascript:void(0)" class="btnDetail" data-id-detail-ksk="'.$data->id_detail_ksk.'">'.$data->ksk_id.' <i class="fas fa-search"></i></a>';
                if ($data->status_ksk == 'PHK') {
                    $statusFormatted = '<span class="badge badge-danger">PHK</span>';
                    if ($data->tanggal_akhir_bekerja >= Carbon::now()) {
                        $actionFormatted = '<button class="btn btn-sm btn-success btnTurnover" data-karyawan-id="'.$data->karyawan_id.'" data-status-ksk="'.$data->status_ksk.'" data-id-detail-ksk="'.$data->id_detail_ksk.'" data-nama-karyawan="'.$data->nama_karyawan.'" data-tgl-akhir-bekerja="'.$data->karyawan->tanggal_selesai.'"><i class="fas fa-plus"></i> Buat Turnover</button>';
                    } else {
                        $actionFormatted = '<button class="btn btn-sm btn-success btnTurnover" data-karyawan-id="'.$data->karyawan_id.'" data-status-ksk="'.$data->status_ksk.'" data-id-detail-ksk="'.$data->id_detail_ksk.'" data-nama-karyawan="'.$data->nama_karyawan.'" data-tgl-akhir-bekerja="'.$data->karyawan->tanggal_selesai.'"><i class="fas fa-plus"></i> Buat Turnover</button>';
                        // $actionFormatted = 'Turnover Tersedia pada tanggal <strong>'.Carbon::parse($data->tanggal_akhir_bekerja)->translatedFormat('d F Y').'</strong>';
                    }
                } elseif ($data->status_ksk == 'PPJ') {
                    $statusFormatted = '<span class="badge badge-success">PERPANJANG</span>';
                    $actionFormatted = '<button class="btn btn-sm btn-success btnKontrak" data-karyawan-id="'.$data->karyawan_id.'" data-status-ksk="'.$data->status_ksk.'" data-id-detail-ksk="'.$data->id_detail_ksk.'" data-nama-karyawan="'.$data->nama_karyawan.'"><i class="fas fa-plus"></i> Buat Kontrak</button>';
                } else {
                    $statusFormatted = '<span class="badge badge-primary">KARYAWAN TETAP</span>';
                    $actionFormatted = '<button class="btn btn-sm btn-success btnKontrak" data-karyawan-id="'.$data->karyawan_id.'" data-status-ksk="'.$data->status_ksk.'" data-id-detail-ksk="'.$data->id_detail_ksk.'" data-nama-karyawan="'.$data->nama_karyawan.'"><i class="fas fa-plus"></i> Buat Kontrak</button>';
                }

                $nestedData['id_detail_ksk'] = $idKSK;
                $nestedData['nama_karyawan'] = $data->nama_departemen;
                $nestedData['nama_departemen'] = $data->nama_jabatan;
                $nestedData['nama_jabatan'] = $data->nama_jabatan;
                $nestedData['nama_posisi'] = $data->nama_posisi;
                $nestedData['tanggal_akhir_bekerja'] = Carbon::parse($data->tanggal_akhir_bekerja)->translatedFormat('d F Y');
                $nestedData['status'] = $statusFormatted;
                $nestedData['aksi'] = $actionFormatted;

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

    public function datatable_history(Request $request)
    {
        $columns = array(
            0 => 'ksk_details.cleareance_id',
            1 => 'karyawans.nama',
            2 => 'ksk_details.nama_departemen',
            3 => 'ksk_details.nama_jabatan',
            4 => 'ksk_details.nama_posisi',
            5 => 'ksk_details.tanggal_akhir_bekerja',
            6 => 'ksk_details.status_ksk',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $settings['start'] = $start;
        $settings['limit'] = $limit;
        $settings['dir'] = $dir;
        $settings['order'] = $order;

        $dataFilter = [];
        $search = $request->input('search.value');
        if (!empty($search)) {
            $dataFilter['search'] = $search;
        }

        $dataFilter['module'] = 'history';
        $detailKSK = DetailKSK::getData($dataFilter, $settings);
        $totalData = DetailKSK::countData($dataFilter);
        $totalFiltered = $totalData;

        $dataTable = [];

        if (!empty($detailKSK)) {
            foreach ($detailKSK as $data) {
                $idKSK = '<a href="javascript:void(0)" class="btnDetail" data-id-detail-ksk="'.$data->id_detail_ksk.'">'.$data->ksk_id.' <i class="fas fa-search"></i></a>';

                if ($data->status_ksk == 'PHK') {
                    $statusFormatted = '<span class="badge badge-danger">PHK</span>';
                } elseif ($data->status_ksk == 'PPJ') {
                    $statusFormatted = '<span class="badge badge-success">PERPANJANG</span>';
                } else {
                    $statusFormatted = '<span class="badge badge-primary">KARYAWAN TETAP</span>';
                }

                $nestedData['id_detail_ksk'] = $idKSK;
                $nestedData['nama_karyawan'] = $data->nama_departemen;
                $nestedData['nama_departemen'] = $data->nama_jabatan;
                $nestedData['nama_jabatan'] = $data->nama_jabatan;
                $nestedData['nama_posisi'] = $data->nama_posisi;
                $nestedData['tanggal_akhir_bekerja'] = Carbon::parse($data->tanggal_akhir_bekerja)->translatedFormat('d F Y');
                $nestedData['status'] = $statusFormatted;

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
}
