<?php

namespace App\Http\Controllers\KSK;

use Throwable;
use Carbon\Carbon;
use App\Models\KSK\KSK;
use App\Models\Karyawan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\KSK\DetailKSK;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ReleaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "KSK-E - Release KSK",
            'page' => 'ksk-release',
        ];
        return view('pages.ksk-e.release.index', $dataPage);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'departemens.nama',
            1 => 'divisis.nama',
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

        $ksk = Karyawan::getDataKSK($dataFilter, $settings);
        $totalData = Karyawan::countDataKSK($dataFilter);
        $totalFiltered = $totalData;

        $dataTable = [];

        if (!empty($ksk)) {
            foreach ($ksk as $data) {

                if (!KSK::where('divisi_id', $data->id_divisi)->where('departemen_id', $data->id_departemen)->whereYear('release_date', $data->tahun_selesai)->whereMonth('release_date', Carbon::createFromFormat('m', str_pad($data->bulan_selesai, 2, '0', STR_PAD_LEFT))->subMonth()->format('m'))->where('parent_id', $data->parent_id)->exists()) {
                    $actionFormatted = '<button class="btn btn-sm btn-success btnRelease" data-id-departemen="'.$data->id_departemen.'" data-id-divisi="'.$data->id_divisi.'" data-parent-id="'.$data->parent_id.'" data-tahun-selesai="'.$data->tahun_selesai.'" data-bulan-selesai="'.str_pad($data->bulan_selesai, 2, '0', STR_PAD_LEFT).'" data-nama-departemen="'.$data->departemen_nama.'" data-nama-divisi="'.$data->divisi_nama.'"><i class="fas fa-plus"></i> Buat KSK</button>';
                } else {
                    $actionFormatted = '-';
                }

                $nestedData['level'] = $data->jabatan_nama;
                $nestedData['divisi'] = $data->divisi_nama;
                $nestedData['departemen'] = $data->departemen_nama;
                $nestedData['release_for'] = Carbon::createFromDate($data->tahun_selesai, $data->bulan_selesai, 1)->format('M Y');
                $nestedData['jumlah_karyawan_habis'] = $data->jumlah_karyawan_habis.' Orang';
                $nestedData['action'] = $actionFormatted;

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

    public function get_karyawans(Request $request)
    {
        $dataValidate = [
            'id_departemen' => ['nullable','exists:departemens,id_departemen'],
            'id_divisi' => ['nullable', 'exists:divisis,id_divisi'],
            'tahun_selesai' => ['required', 'numeric'],
            'bulan_selesai' => ['required', 'numeric'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $id_departemen = $request->id_departemen;
        $id_divisi = $request->id_divisi;
        $parent_id = $request->parent_id;
        $tahun_selesai = $request->tahun_selesai;
        $bulan_selesai = $request->bulan_selesai;

        try {
            $dataFilter = [
                'id_departemen' => $id_departemen,
                'id_divisi' => $id_divisi,
                'parent_id' => $parent_id,
                'tahun_selesai' => $tahun_selesai,
                'bulan_selesai' => $bulan_selesai,
            ];

            $datas = Karyawan::getKaryawanKsk($dataFilter);
            $html = view('layouts.partials.ksk-list-karyawan-release', ['datas' => $datas])->render();
            return response()->json(['message' => 'success', 'data' => $datas, 'html' => $html], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $dataValidate = [

            //Header
            'tahun_selesai_header' => ['required', 'date_format:Y'],
            'bulan_selesai_header' => ['required', 'date_format:m'],
            'id_departemen_header' => ['nullable', 'exists:departemens,id_departemen'],
            'id_divisi_header' => ['nullable', 'exists:divisis,id_divisi'],
            'nama_divisi_header' => ['nullable'],
            'nama_departemen_header' => ['nullable'],
            'parent_id_header' => ['required', 'exists:posisis,id_posisi'],

            //Detail
            'id_karyawan.*' => ['required', 'exists:karyawans,id_karyawan'],
            'ni_karyawan.*' => ['required'],
            'jenis_kontrak.*' => ['nullable'],
            'nama_karyawan.*' => ['nullable'],
            'posisi_id.*' => ['nullable', 'exists:posisis,id_posisi'],
            'nama_posisi.*' => ['nullable'],
            'jabatan_id.*' => ['nullable', 'exists:jabatans,id_jabatan'],
            'nama_jabatan.*' => ['nullable'],
            'jumlah_sakit.*' => ['required', 'numeric', 'min:0'],
            'jumlah_izin.*' => ['required', 'numeric', 'min:0'],
            'jumlah_alpa.*' => ['required', 'numeric', 'min:0'],
            'jumlah_surat_peringatan.*' => ['required', 'numeric', 'min:0'],

        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $tahun_selesai = $request->tahun_selesai_header;
        $bulan_selesai = $request->bulan_selesai_header;
        $id_departemen = $request->id_departemen_header;
        $id_divisi = $request->id_divisi_header;
        $nama_divisi = $request->nama_divisi_header;
        $nama_departemen = $request->nama_departemen_header;
        $parent_id = $request->parent_id_header;

        $id_karyawan = $request->id_karyawan;
        $ni_karyawan = $request->ni_karyawan;
        $nama_karyawan = $request->nama_karyawan;
        $jenis_kontrak = $request->jenis_kontrak;
        $posisi_id = $request->posisi_id;
        $nama_posisi = $request->nama_posisi;
        $jabatan_id = $request->jabatan_id;
        $nama_jabatan = $request->nama_jabatan;
        $jumlah_sakit = $request->jumlah_sakit;
        $jumlah_izin = $request->jumlah_izin;
        $jumlah_alpa = $request->jumlah_alpa;
        $jumlah_surat_peringatan = $request->jumlah_surat_peringatan;

        DB::beginTransaction();
        try {

            $ksk = KSK::create([
                'id_ksk' => 'KSK-' . Str::random(4).'-'. date('YmdHis'),
                'organisasi_id' => auth()->user()->organisasi_id,
                'divisi_id' => $id_divisi,
                'nama_divisi' => $nama_divisi,
                'departemen_id' => $id_departemen,
                'nama_departemen' => $nama_departemen,
                'parent_id' => $parent_id,
                'release_date' => Carbon::now()->format('Y-m-d'),
                'released_by' => 'HRD',
                'released_at' => Carbon::now(),
            ]);

            $detail_ksk = [];
            if (!empty($id_karyawan)) {
                foreach ($id_karyawan as $index => $karyawan) {
                    $detail_ksk[] = [
                        'ksk_id' => $ksk->id_ksk,
                        'organisasi_id' => auth()->user()->organisasi_id,
                        'divisi_id' => $id_divisi,
                        'nama_divisi' => $nama_divisi,
                        'departemen_id' => $id_departemen,
                        'nama_departemen' => $nama_departemen,
                        'karyawan_id' => $karyawan,
                        'ni_karyawan' => $ni_karyawan[$index],
                        'nama_karyawan' => $nama_karyawan[$index],
                        'jumlah_sakit' => $jumlah_sakit[$index],
                        'jumlah_izin' => $jumlah_izin[$index],
                        'jumlah_alpa' => $jumlah_alpa[$index],
                        'jumlah_surat_peringatan' => $jumlah_surat_peringatan[$index],
                        'jenis_kontrak' => $jenis_kontrak[$index],
                        'posisi_id' => $posisi_id[$index],
                        'nama_posisi' => $nama_posisi[$index],
                        'jabatan_id' => $jabatan_id[$index],
                        'nama_jabatan' => $nama_jabatan[$index],
                    ];
                }

                DetailKSK::insert($detail_ksk);
            } else {
                DB::rollBack();
                return response()->json(['message' => 'Karyawan tidak ditemukan'], 402);
            }
            DB::commit();
            return response()->json(['message' => 'KSK berhasil di Release'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
