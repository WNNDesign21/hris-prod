<?php

namespace App\Http\Controllers\KSK;

use Exception;
use Throwable;
use Carbon\Carbon;
use App\Models\Posisi;
use App\Models\Kontrak;
use App\Models\Karyawan;
use App\Models\Turnover;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\KSK\DetailKSK;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
                $tempat = auth()->user()->organisasi_id == 1 ? 'Purwakarta' : 'Karawang';
                // $jenis_kontrak = $data->status_ksk == 'PPJ' ? $data->karyawan->jenis_kontrak : 'PKWTT';
                $jenis_kontrak = $data->jenis_kontrak;
                $durasi = $data->status_ksk == 'PPJ' || $data->status_ksk == 'PPJMG' ? $data->durasi_renewal : 0;
                $id_posisi = $data->karyawan->posisi[0]->id_posisi;
                $nama_posisi = $data->karyawan->posisi[0]->nama;
                $idKSK = '<a href="javascript:void(0)" class="btnDetail" data-id-ksk-detail="'.$data->id_ksk_detail.'">'.$data->ksk_id.' <i class="fas fa-search"></i></a>';
                if ($data->status_ksk == 'PHK') {
                    $statusFormatted = '<span class="badge badge-danger">PHK</span>';
                    if ($data->cleareance->tanggal_akhir_bekerja <= Carbon::now()) {
                        $actionFormatted = '<button class="btn btn-sm btn-success btnTurnover" data-karyawan-id="'.$data->karyawan_id.'" data-status-ksk="'.$data->status_ksk.'" data-id-ksk-detail="'.$data->id_ksk_detail.'" data-nama-karyawan="'.$data->nama_karyawan.'" data-tgl-akhir-bekerja="'.$data->karyawan->tanggal_selesai.'"><i class="fas fa-plus"></i> Buat Turnover</button>';
                    } else {
                        // $actionFormatted = '<button class="btn btn-sm btn-success btnTurnover" data-karyawan-id="'.$data->karyawan_id.'" data-status-ksk="'.$data->status_ksk.'" data-id-ksk-detail="'.$data->id_ksk_detail.'" data-nama-karyawan="'.$data->nama_karyawan.'" data-tgl-akhir-bekerja="'.$data->karyawan->tanggal_selesai.'"><i class="fas fa-plus"></i> Buat Turnover</button>';
                        $actionFormatted = 'Turnover Tersedia pada tanggal <strong>'.Carbon::parse($data->cleareance->tanggal_akhir_bekerja)->translatedFormat('d F Y').'</strong>';
                    }
                } elseif ($data->status_ksk == 'PPJ') {
                    $statusFormatted = '<span class="badge badge-success">PERPANJANG (PKWT)</span>';
                    $actionFormatted = '<button class="btn btn-sm btn-success btnKontrak" data-karyawan-id="'.$data->karyawan_id.'" data-status-ksk="'.$data->status_ksk.'" data-id-ksk-detail="'.$data->id_ksk_detail.'" data-nama-karyawan="'.$data->nama_karyawan.'" data-tgl-renewal-kontrak="'.$data->tanggal_renewal_kontrak.'" data-tempat="'.$tempat.'" data-jenis-kontrak="'.$jenis_kontrak.'" data-durasi-renewal="'.$durasi.'" data-id-posisi="'.$id_posisi.'" data-nama-posisi="'.$nama_posisi.'"><i class="fas fa-plus"></i> Buat Kontrak</button>';
                } elseif ($data->status_ksk == 'PPJMG') {
                    $statusFormatted = '<span class="badge badge-success">PERPANJANG (MAGANG)</span>';
                    $actionFormatted = '<button class="btn btn-sm btn-success btnKontrak" data-karyawan-id="'.$data->karyawan_id.'" data-status-ksk="'.$data->status_ksk.'" data-id-ksk-detail="'.$data->id_ksk_detail.'" data-nama-karyawan="'.$data->nama_karyawan.'" data-tgl-renewal-kontrak="'.$data->tanggal_renewal_kontrak.'" data-tempat="'.$tempat.'" data-jenis-kontrak="'.$jenis_kontrak.'" data-durasi-renewal="'.$durasi.'" data-id-posisi="'.$id_posisi.'" data-nama-posisi="'.$nama_posisi.'"><i class="fas fa-plus"></i> Buat Kontrak</button>';
                } else {
                    $statusFormatted = '<span class="badge badge-primary">KARYAWAN TETAP</span>';
                    $actionFormatted = '<button class="btn btn-sm btn-success btnKontrak" data-karyawan-id="'.$data->karyawan_id.'" data-status-ksk="'.$data->status_ksk.'" data-id-ksk-detail="'.$data->id_ksk_detail.'" data-nama-karyawan="'.$data->nama_karyawan.'" data-tgl-renewal-kontrak="'.$data->tanggal_renewal_kontrak.'" data-tempat="'.$tempat.'" data-jenis-kontrak="'.$jenis_kontrak.'" data-durasi-renewal="'.$durasi.'" data-id-posisi="'.$id_posisi.'" data-nama-posisi="'.$nama_posisi.'"><i class="fas fa-plus"></i> Buat Kontrak</button>';
                }

                $nestedData['id_detail_ksk'] = $idKSK;
                $nestedData['nama_karyawan'] = $data->nama_departemen;
                $nestedData['nama_departemen'] = $data->nama_jabatan;
                $nestedData['nama_jabatan'] = $data->nama_jabatan;
                $nestedData['nama_posisi'] = $data->nama_posisi;
                $nestedData['tanggal_akhir_bekerja'] = $data->cleareance ? Carbon::parse($data->cleareance->tanggal_akhir_bekerja)->translatedFormat('d F Y') : '-';
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
                $idKSK = '<a href="javascript:void(0)" class="btnDetail" data-id-ksk-detail="'.$data->id_ksk_detail.'">'.$data->ksk_id.' <i class="fas fa-search"></i></a>';

                if ($data->status_ksk == 'PHK') {
                    $statusFormatted = '<span class="badge badge-danger">PHK</span>';
                } elseif ($data->status_ksk == 'PPJ') {
                    $statusFormatted = '<span class="badge badge-success">PERPANJANG (PKWT)</span>';
                } elseif ($data->status_ksk == 'PPJMG') {
                    $statusFormatted = '<span class="badge badge-success">PERPANJANG (MAGANG)</span>';
                } else {
                    $statusFormatted = '<span class="badge badge-primary">KARYAWAN TETAP</span>';
                }

                $nestedData['id_detail_ksk'] = $idKSK;
                $nestedData['nama_karyawan'] = $data->nama_departemen;
                $nestedData['nama_departemen'] = $data->nama_jabatan;
                $nestedData['nama_jabatan'] = $data->nama_jabatan;
                $nestedData['nama_posisi'] = $data->nama_posisi;
                $nestedData['tanggal_akhir_bekerja'] = Carbon::parse($data->karyawan->tanggal_selesai)->translatedFormat('d F Y');
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

    public function store_turnover(Request $request)
    {
        $dataValidate = [
            'id_ksk_detailTurnover' => ['required', 'exists:ksk_details,id_ksk_detail'],
            'karyawan_idTurnover' => ['required', 'exists:karyawans,id_karyawan'],
            'status_karyawanTurnover' => ['required'],
            'tanggal_keluarTurnover' => ['required','date'],
            'keteranganTurnover' => ['nullable'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $id_ksk_detail = $request->id_ksk_detailTurnover;
        $karyawan_id = $request->karyawan_idTurnover;
        $status_karyawan = $request->status_karyawanTurnover;
        $tanggal_keluar = $request->tanggal_keluarTurnover;
        $keterangan = $request->keteranganTurnover;
        $organisasi_id = auth()->user()->organisasi_id;
        $jumlah_aktif_karyawan_terakhir = Karyawan::organisasi($organisasi_id)->where('status_karyawan', 'AT')->count();

        DB::beginTransaction();
        try {

            $turnover = Turnover::create([
                'karyawan_id' => $karyawan_id,
                'status_karyawan' => $status_karyawan,
                'tanggal_keluar' => $tanggal_keluar,
                'keterangan' => $keterangan,
                'organisasi_id' => $organisasi_id,
                'jumlah_aktif_karyawan_terakhir' => $jumlah_aktif_karyawan_terakhir,
            ]);

            $karyawan = Karyawan::find($karyawan_id);
            $karyawan->status_karyawan = $status_karyawan;
            if($status_karyawan == 'MD' || $status_karyawan == 'TM'){
                $karyawan->tanggal_selesai = $tanggal_keluar;
            }
            $karyawan->save();

            DB::commit();
            return response()->json(['message' => 'Data Turnover berhasil ditambahkan!'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage() ], 402);
        }
    }

    public function store_kontrak(Request $request)
    {
        $dataValidate = [
            'karyawan_idKontrak' => ['required', 'exists:karyawans,id_karyawan'],
            'jenisKontrak' => ['required', 'in:PKWT,PKWTT,MAGANG'],
            'posisiKontrak' => ['required', 'exists:posisis,id_posisi'],
            'nama_posisiKontrak' => ['nullable', 'string'],
            'durasiKontrak' => ['numeric','min:0'],
            'salaryKontrak' => ['numeric','required'],
            'tanggal_mulaiKontrak' => ['required', 'date'],
            'tanggal_selesaiKontrak' => ['nullable', 'date'],
            'issued_dateKontrak' => ['required', 'date'],
            'tempat_administrasiKontrak' => ['required', 'in:Karawang,Purwakarta'],
            'no_suratKontrak' => ['required', 'digits:3'],
            'id_detail_kskKontrak' => ['required', 'exists:ksk_details,id_ksk_detail'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $karyawan_id = $request->karyawan_idKontrak;
        $jenis = $request->jenisKontrak;
        $posisi_id = $request->posisiKontrak;
        $nama_posisi = $request->nama_posisiKontrak;
        $durasi = $request->durasiKontrak;
        $salary = $request->salaryKontrak;
        $deskripsi = $request->deskripsiKontrak;
        $tanggal_mulai = $request->tanggal_mulaiKontrak;
        $tanggal_selesai = $request->tanggal_selesaiKontrak;
        $issued_date = $request->issued_dateKontrak;
        $tempat_administrasi = $request->tempat_administrasiKontrak;
        $no_surat = $request->no_suratKontrak;
        $organisasi_id = auth()->user()->organisasi_id;
        $id_ksk_detail = $request->id_detail_kskKontrak;

        DB::beginTransaction();
        try{
            $detailKSK = DetailKSK::find($id_ksk_detail);

            //CEK APAKAH DIA SUDAH ADA KONTRAK SEBELUMNYA ATAU BELUM
            $is_kontrak_exist = Kontrak::where('karyawan_id', $karyawan_id)
                ->where('status', 'DONE')
                ->where(function ($query) use ($tanggal_mulai, $tanggal_selesai) {
                    $query->whereBetween('tanggal_mulai', [$tanggal_mulai, $tanggal_selesai])
                          ->orWhereBetween('tanggal_selesai', [$tanggal_mulai, $tanggal_selesai])
                          ->orWhere(function ($query) use ($tanggal_mulai, $tanggal_selesai) {
                              $query->where('tanggal_mulai', '<=', $tanggal_mulai)
                                    ->where('tanggal_selesai', '>=', $tanggal_selesai);
                          });
                })
                ->first();

            if ($is_kontrak_exist) {
                DB::commit();
                return response()->json(['message' => 'Data kontrak sudah ada!'], 402);
            }

            if($jenis !== 'PKWTT'){

                if ($durasi == 0) {
                    DB::commit();
                    return response()->json(['message' => 'Durasi tidak boleh kosong!'], 402);
                }

                $no_surat_int = intval($no_surat);
                $kry = Karyawan::find($karyawan_id);
                $kontrak_karyawan = $kry->kontrak()->where('status', 'DONE')->count() + 1;
                $kry->jenis_kontrak = $jenis;
                $bulan_romawi = $this->angka_to_romawi(Carbon::parse($tanggal_mulai)->month);
                $hrd = $tempat_administrasi == 'Karawang' ? 'HRD-TCF3' : 'HRD-TCF2';
                $jenis_on_surat = ($jenis == 'MAGANG' ? 'MG' : $jenis).($jenis == 'PKWT' || $jenis == 'MAGANG' ? '-'.$this->angka_to_romawi($kontrak_karyawan) : '');
                $tahun = Carbon::parse($tanggal_mulai)->format('Y');
                $no_surat_text = 'No. ' . str_pad($no_surat_int, 3, '0', STR_PAD_LEFT) . '/' . $jenis_on_surat . '/' . $hrd . '/'.$bulan_romawi.'/' . $tahun;

                $kry->save();
                $kontrak = Kontrak::create([
                    'id_kontrak' => 'KONTRAK-'. Str::random(4) . '-' . (now()->timestamp + 1),
                    'karyawan_id' => $karyawan_id,
                    'organisasi_id' => $organisasi_id,
                    'posisi_id' => $posisi_id,
                    'nama_posisi' => $nama_posisi ? $nama_posisi : Posisi::find($posisi_id)?->nama,
                    'jenis' => $jenis,
                    'durasi' => $durasi,
                    'salary' => $salary,
                    'issued_date' => $issued_date,
                    'tempat_administrasi' => $tempat_administrasi,
                    'no_surat' => $no_surat_text,
                    'deskripsi' => $deskripsi,
                    'tanggal_mulai' => $tanggal_mulai,
                    'tanggal_selesai' => $tanggal_selesai,
                    'isReactive' => 'N',
                    'status' => 'DONE',
                ]);
                $detailKSK->kontrak_id = $kontrak->id_kontrak;
                $detailKSK->save();
            } else {
                $no_surat_int = intval($no_surat);
                $kry = Karyawan::find($karyawan_id);
                $kontrak_karyawan = $kry->kontrak()->where('status', 'DONE')->count() + 1;
                $kry->jenis_kontrak = $jenis;
                $bulan_romawi = $this->angka_to_romawi(Carbon::parse($tanggal_mulai)->month);
                $hrd = 'TCF';
                $jenis_on_surat = 'SKP';
                $tahun = Carbon::parse($tanggal_mulai)->format('Y');
                $no_surat_text = 'No. ' . str_pad($no_surat_int, 3, '0', STR_PAD_LEFT) . '/' . $jenis_on_surat . '/' . $hrd . '/'.$bulan_romawi.'/' . $tahun;
                $kry->save();
                $kontrak = Kontrak::create([
                    'id_kontrak' => 'KONTRAK-'. Str::random(4) . '-' . now()->timestamp,
                    'karyawan_id' => $karyawan_id,
                    'organisasi_id' => $organisasi_id,
                    'posisi_id' => $posisi_id,
                    'nama_posisi' => $nama_posisi ? $nama_posisi : Posisi::find($posisi_id)->nama,
                    'jenis' => $jenis,
                    'durasi' => null,
                    'salary' => $salary,
                    'issued_date' => $issued_date,
                    'no_surat' => $no_surat_text,
                    'tempat_administrasi' => $tempat_administrasi,
                    'deskripsi' => $deskripsi,
                    'tanggal_mulai' => $tanggal_mulai,
                    'tanggal_selesai' => null,
                    'isReactive' => 'N',
                    'status' => 'DONE',
                ]);
                $detailKSK->kontrak_id = $kontrak->id_kontrak;
                $detailKSK->save();
            }
            DB::commit();
            return response()->json(['message' => 'Kontrak Berhasil Ditambahkan!'],200);
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

    // Custom Function
    function angka_to_romawi($angka) {
        $romawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
            13 => 'XIII',
            14 => 'XIV',
            15 => 'XV',
            16 => 'XVI',
            17 => 'XVII',
            18 => 'XVIII',
            19 => 'XIX',
            20 => 'XX'
        ];

        return $romawi[$angka] ?? null;
    }
}
