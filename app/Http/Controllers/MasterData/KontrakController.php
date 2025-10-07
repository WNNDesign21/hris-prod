<?php

namespace App\Http\Controllers\MasterData;

use Throwable;
use Carbon\Carbon;
use App\Models\Event;
use App\Models\Posisi;
use App\Models\Kontrak;
use App\Models\Karyawan;
use App\Models\Template;
use App\Models\Departemen;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\UploadKontrakJob;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PhpOffice\PhpSpreadsheet\IOFactory as PhpSpreadsheetIOFactory;
use App\Models\JenisKontrak;

class KontrakController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departemen = Departemen::all();
        $dataPage = [
            'pageTitle' => "Master Data - Kontrak",
            'page' => 'masterdata-kontrak',
            'departemen' => $departemen
        ];
        return view('pages.master-data.kontrak.index', $dataPage);
    }

    public function datatable(Request $request)
    {

        $columns = array(
            0 => 'kontraks.id_kontrak',
            1 => 'karyawans.nama',
            2 => 'departemens.nama',
            3 => 'kontraks.nama_posisi',
            4 => 'kontraks.no_surat',
            5 => 'kontraks.issued_date',
            6 => 'kontraks.jenis',
            7 => 'kontraks.status',
            8 => 'kontraks.durasi',
            9 => 'kontraks.salary',
            10 => 'kontraks.tanggal_mulai',
            11 => 'kontraks.tanggal_selesai',
        );

        $totalData = Karyawan::where('status_karyawan', '=', 'AT')->count();

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

        if (!empty($request->input('nama'))) {
            $dataFilter['nama'] = $request->input('nama');
        }

        if (!empty($request->input('noSurat'))) {
            $dataFilter['noSurat'] = $request->input('noSurat');
        }

        if (!empty($request->input('departemen'))) {
            $dataFilter['departemen'] = $request->input('departemen');
        }

        if (!empty($request->input('jenisKontrak'))) {
            $dataFilter['jenisKontrak'] = $request->input('jenisKontrak');
        }
        if (!empty($request->input('statusKontrak'))) {
            $dataFilter['statusKontrak'] = $request->input('statusKontrak');
        }

        if (!empty($request->input('namaPosisi'))) {
            $dataFilter['namaPosisi'] = $request->input('namaPosisi');
        }

        if (!empty($request->input('tanggalMulaistart'))) {
            $dataFilter['tanggalMulaistart'] = $request->input('tanggalMulaistart');
        }
        if (!empty($request->input('tanggalMulaiend'))) {
            $dataFilter['tanggalMulaiend'] = $request->input('tanggalMulaiend');
        }

        if (!empty($request->input('attachment'))) {
            $dataFilter['attachment'] = $request->input('attachment');
        }

        if (!empty($request->input('evidence'))) {
            $dataFilter['evidence'] = $request->input('evidence');
        }

        $kontrak = Kontrak::getData($dataFilter, $settings);
        $totalFiltered = Kontrak::countData($dataFilter);

        $dataTable = [];

        if (!empty($kontrak)) {
            foreach ($kontrak as $data) {
                $nestedData['id_kontrak'] = $data->id_kontrak;
                $nestedData['nama'] = $data->nama_karyawan;
                $nestedData['departemen'] = $data->nama_departemen;
                $nestedData['nama_posisi'] = $data->nama_posisi ? $data->nama_posisi : $data->nama_posisis;
                $nestedData['no_surat'] = $data->no_surat;
                $nestedData['issued_date'] = $data->issued_date;
                $nestedData['jenis'] = $data->jenis;
                $nestedData['status'] = $data->status == 'DONE' ? '<span class="badge badge-pill badge-success">'.$data->status.'</span>' : '<span class="badge badge-pill badge-warning">'.$data->status.'</span>';
                $nestedData['durasi'] = $data->durasi.' Bulan';
                $nestedData['salary'] = $data->salary;
                // $nestedData['status_change_by'] = '<small class="text-bold">'.$data->status_change_by.'</small> - '.'<br>'.'<small class="text-primary">'.$data->status_change_date.'</small>';
                $nestedData['tanggal_mulai'] = $data->tanggal_mulai_kontrak;
                $nestedData['tanggal_selesai'] = $data->tanggal_selesai_kontrak;
                $nestedData['attachment'] = $data->attachment ? '<div class="btn-group btn-group-sm"><button data-type="attachment" data-id="'.$data->id_kontrak.'" class="btn btn-sm btn-primary btn-file-change" type="button"><i class="fas fa-upload"></i> Change</button><input type="file" name="attachment" id="attachment_change_'.$data->id_kontrak.'" class="d-none"><a href="'.asset('storage/'.$data->attachment).'" target="_blank" class="btn btn-sm btn-secondary"><i class="fas fa-download"></i> Download</a></div>' : '<button data-id="'.$data->id_kontrak.'" data-type="attachment" class="btn btn-sm btn-primary btn-file" type="button">Upload</button><input type="file" name="attachment" id="attachment_'.$data->id_kontrak.'" class="d-none">';
                $nestedData['evidence'] = $data->evidence ? '<div class="btn-group btn-group-sm"><button data-type="evidence" data-id="'.$data->id_kontrak.'" class="btn btn-sm btn-primary btn-file-change" type="button"><i class="fas fa-upload"></i> Change</button><input type="file" name="evidence" id="evidence_change_'.$data->id_kontrak.'" class="d-none"><a href="'.asset('storage/'.$data->evidence).'" target="_blank" class="btn btn-sm btn-secondary"><i class="fas fa-download"></i> Download</a></div>' : '<button data-id="'.$data->id_kontrak.'" data-type="evidence" class="btn btn-sm btn-primary btn-file" type="button">Upload</button><input type="file" name="evidence" id="evidence_'.$data->id_kontrak.'" class="d-none">';
                $nestedData['aksi'] = '
                <div class="btn-group btn-group-sm">'.
                    ($data->attachment !== null && $data->evidence !== null && $data->status !== 'DONE' ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-success btnDone" data-id="'.$data->id_kontrak.'" data-isreactive="'.$data->isReactive.'"><i class="far fa-check-circle"></i> Done</button>' : '').
                    ($data->status !== 'DONE' ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnEdit" data-id="'.$data->id_kontrak.'"><i class="fas fa-edit"></i> Edit</button>' : '').
                    ($data->status !== 'DONE' ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnDelete" data-id="'.$data->id_kontrak.'"><i class="fas fa-trash-alt"></i> Hapus </button>' : '').
                    '<a class="waves-effect waves-light btn btn-sm btn-info" href="'.url('master-data/kontrak/download-kontrak-kerja/'.$data->id_kontrak).'" target="_blank"><i class="fas fa-download"></i> Template</a>
                </div>
                ';

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
            'karyawan_id.*' => ['required'],
            'jenis' => ['required'],
            'posisi' => ['required'],
            'durasi' => ['numeric','nullable'],
            'salary' => ['numeric','required'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date'],
            'issued_date' => ['required', 'date'],
            'tempat_administrasi' => ['required'],
            'no_surat' => ['required', 'digits:3'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $karyawan_id = $request->karyawan_id;
        $jenisNama = $request->jenis;
        $posisi_id = $request->posisi;
        $nama_posisi = $request->nama_posisi;
        $durasi = $request->durasi;
        $salary = $request->salary;
        $deskripsi = $request->deskripsi;
        $tanggal_mulai = $request->tanggal_mulai;
        $tanggal_selesai = $request->tanggal_selesai;
        $issued_date = $request->issued_date;
        $tempat_administrasi = $request->tempat_administrasi;
        $no_surat = $request->no_surat;
        $isReactive = $request->isReactive;
        $organisasi_id = auth()->user()->organisasi_id;

        DB::beginTransaction();
        try{

            //CEK APAKAH DIA SUDAH ADA KONTRAK SEBELUMNYA ATAU BELUM
            $is_kontrak_exist = Kontrak::where('karyawan_id', $karyawan_id)->where('status', 'DONE')->orderBy('tanggal_selesai', 'DESC')->first();
            if($isReactive == 'Y'){
                if(!$is_kontrak_exist){
                    DB::commit();
                    return response()->json(['message' => 'Karyawan belum memiliki Kontrak untuk memilih Reactive!'], 402);
                }
            }

            $jenisKontrak = JenisKontrak::where('nama', $jenisNama)->first();

            // Automatically end the previous active contract
            foreach ($karyawan_id as $karyawan) {
                $active_kontrak = Kontrak::where('karyawan_id', $karyawan)
                    ->where('status', 'DONE')
                    ->where(function ($query) {
                        $query->where('tanggal_selesai', '>=', Carbon::now()->format('Y-m-d'))
                              ->orWhereNull('tanggal_selesai');
                    })
                    ->orderBy('tanggal_mulai', 'desc')
                    ->first();

                if ($active_kontrak) {
                    $active_kontrak->tanggal_selesai = Carbon::parse($tanggal_mulai)->subDay()->format('Y-m-d');
                    $active_kontrak->save();
                }
            }

            if($jenisNama !== 'PKWTT' && $jenisNama !== 'PENGKARYAAN'){

                if ($durasi == 0) {
                    DB::commit();
                    return response()->json(['message' => 'Durasi tidak boleh kosong!'], 402);
                }

                $no_surat_int = intval($no_surat);
                foreach ($karyawan_id as $karyawan) {
                    $kry = Karyawan::find($karyawan);
                    $kontrak_karyawan = $kry->kontrak()->where('status', 'DONE')->count() + 1;

                    //No Surat Text
                    $kry->jenis_kontrak = $jenisNama;
                    $bulan_romawi = $this->angka_to_romawi(Carbon::parse($tanggal_mulai)->month);
                    $hrd = $tempat_administrasi == 'ASI PLANT-1' ? 'HRGA' : 'HRGAASI2';
                    $jenis_on_surat = ($jenisNama == 'MAGANG' ? 'MG' : $jenisNama).($jenisNama == 'PKWT' || $jenisNama == 'MAGANG' ? '-'.$this->angka_to_romawi($kontrak_karyawan) : '');
                    $tahun = Carbon::parse($tanggal_mulai)->format('Y');
                    $no_surat_text = 'No. ' . str_pad($no_surat_int, 3, '0', STR_PAD_LEFT) . '/' . $jenis_on_surat . '/' . $hrd . '/'.$bulan_romawi.'/' . $tahun;

                    $kry->save();
                    $kontrak = Kontrak::create([
                        'id_kontrak' => 'KONTRAK-'. Str::random(4) . '-' . (now()->timestamp + 1),
                        'karyawan_id' => $karyawan,
                        'organisasi_id' => $organisasi_id,
                        'posisi_id' => $posisi_id,
                        'nama_posisi' => $nama_posisi ? $nama_posisi : Posisi::find($posisi_id)?->nama,
                        'jenis_kontrak_id' => $jenisKontrak ? $jenisKontrak->id : null,
                        'durasi' => $durasi,
                        'salary' => $salary,
                        'issued_date' => $issued_date,
                        'tempat_administrasi' => $tempat_administrasi,
                        'no_surat' => $no_surat_text,
                        'deskripsi' => $deskripsi,
                        'tanggal_mulai' => $tanggal_mulai,
                        'tanggal_selesai' => $tanggal_selesai,
                        'isReactive' => $isReactive == 'Y' ? 'Y' : 'N',
                        'tanggal_mulai_before' => $isReactive == 'Y' ? $kry->tanggal_mulai : null,
                        'tanggal_selesai_before' => $isReactive == 'Y' ? $kry->tanggal_selesai : null,
                    ]);
                    $no_surat_int++;
                }

            } else {

                $no_surat_int = intval($no_surat);
                foreach($karyawan_id as $karyawan){
                    $kry = Karyawan::find($karyawan);
                    $kontrak_karyawan = $kry->kontrak()->where('status', 'DONE')->count() + 1;

                    //No Surat Text
                    $kry->jenis_kontrak = $jenisNama;
                    $bulan_romawi = $this->angka_to_romawi(Carbon::parse($tanggal_mulai)->month);
                    $hrd = 'ASI';
                    $jenis_on_surat = 'SKP';
                    $tahun = Carbon::parse($tanggal_mulai)->format('Y');
                    $no_surat_text = 'No. ' . str_pad($no_surat_int, 3, '0', STR_PAD_LEFT) . '/' . $jenis_on_surat . '/' . $hrd . '/'.$bulan_romawi.'/' . $tahun;
                    $kry->save();
                    $kontrak = Kontrak::create([
                        'id_kontrak' => 'KONTRAK-'. Str::random(4) . '-' . now()->timestamp,
                        'karyawan_id' => $karyawan,
                        'organisasi_id' => $organisasi_id,
                        'posisi_id' => $posisi_id,
                        'nama_posisi' => $nama_posisi ? $nama_posisi : Posisi::find($posisi_id)->nama,
                        'jenis_kontrak_id' => $jenisKontrak ? $jenisKontrak->id : null,
                        'durasi' => null,
                        'salary' => $salary,
                        'issued_date' => $issued_date,
                        'no_surat' => $no_surat_text,
                        'tempat_administrasi' => $tempat_administrasi,
                        'deskripsi' => $deskripsi,
                        'tanggal_mulai' => $tanggal_mulai,
                        'tanggal_selesai' => null,
                        'isReactive' => $isReactive == 'Y' ? 'Y' : 'N',
                        'tanggal_mulai_before' => $isReactive == 'Y' ? $kry->tanggal_mulai : null,
                        'tanggal_selesai_before' => $isReactive == 'Y' ? $kry->tanggal_selesai : null,
                    ]);
                }
            }
            DB::commit();
            return response()->json(['message' => 'Kontrak Berhasil Ditambahkan!'],200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    //Buatkan saya sebuah fungsi untuk merubah bulan dalam tanggal menjadi sebuah angka romawi I-XII
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
    public function update(Request $request, string $id_kontrak)
    {
        $dataValidate = [
            'jenis_kontrak_id' => ['required'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date'],
            'posisi_id' => ['required'],
            'salary' => ['numeric', 'required'],
            // Add other validations if they are part of the form
        ];

        $validator = Validator::make($request->all(), $dataValidate);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $kontrak = Kontrak::with(['karyawan', 'jenisKontrak', 'posisi'])->find($id_kontrak);

            if (!$kontrak) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Kontrak tidak ditemukan!');
            }

            $old_posisi_id = $kontrak->posisi_id;
            $old_jenis_kontrak_id = $kontrak->jenis_kontrak_id;

            $new_jenis_kontrak_id = $request->jenis_kontrak_id;
            $new_posisi_id = $request->posisi_id;
            $new_tanggal_mulai = $request->tanggal_mulai;
            $new_tanggal_selesai = $request->tanggal_selesai;
            $new_salary = $request->salary;

            $jenisKontrakBaru = JenisKontrak::find($new_jenis_kontrak_id);
            $posisiBaru = Posisi::find($new_posisi_id);

            // Handle PKWTT/PENGKARYAAN contract position change
            if (($kontrak->jenisKontrak->nama === 'PKWTT' || $kontrak->jenisKontrak->nama === 'PENGKARYAAN') && $old_posisi_id !== $new_posisi_id) {
                // End the old contract
                $kontrak->tanggal_selesai = Carbon::now()->format('Y-m-d');
                $kontrak->status = 'DONE';
                $kontrak->save();

                // Create a new contract
                $new_kontrak = Kontrak::create([
                    'id_kontrak' => 'KONTRAK-' . Str::random(4) . '-' . now()->timestamp,
                    'karyawan_id' => $kontrak->karyawan_id,
                    'organisasi_id' => $kontrak->organisasi_id,
                    'posisi_id' => $new_posisi_id,
                    'nama_posisi' => $posisiBaru->nama,
                    'jenis_kontrak_id' => $new_jenis_kontrak_id,
                    'durasi' => null, // PKWTT/PENGKARYAAN has no duration
                    'salary' => $new_salary,
                    'issued_date' => Carbon::now()->format('Y-m-d'),
                    'no_surat' => 'AUTO-GENERATED', // This needs to be properly generated
                    'tempat_administrasi' => $kontrak->tempat_administrasi,
                    'deskripsi' => 'Perubahan Posisi',
                    'tanggal_mulai' => Carbon::now()->format('Y-m-d'),
                    'tanggal_selesai' => null, // PKWTT/PENGKARYAAN has no end date
                    'isReactive' => 'N',
                    'tanggal_mulai_before' => null,
                    'tanggal_selesai_before' => null,
                ]);

                // Update karyawan's current position
                $kontrak->karyawan->posisi_id = $new_posisi_id;
                $kontrak->karyawan->jenis_kontrak = $jenisKontrakBaru->nama;
                $kontrak->karyawan->save();

                // Update karyawan_posisi pivot table
                DB::table('karyawan_posisi')->where('karyawan_id', $kontrak->karyawan_id)->update(['posisi_id' => $new_posisi_id]);

            } else {
                // Regular update for other contract types or no position change
                $kontrak->jenis_kontrak_id = $new_jenis_kontrak_id;
                $kontrak->tanggal_mulai = $new_tanggal_mulai;
                $kontrak->tanggal_selesai = $new_tanggal_selesai;
                $kontrak->posisi_id = $new_posisi_id;
                $kontrak->nama_posisi = $posisiBaru->nama;
                $kontrak->salary = $new_salary;

                // Update durasi if not PKWTT/PENGKARYAAN
                if ($jenisKontrakBaru->nama !== 'PKWTT' && $jenisKontrakBaru->nama !== 'PENGKARYAAN') {
                    $kontrak->durasi = $request->durasi; // Assuming 'durasi' is passed in the request for non-PKWTT/PENGKARYAAN
                } else {
                    $kontrak->durasi = null;
                    $kontrak->tanggal_selesai = null;
                }

                $kontrak->save();

                // Update karyawan's current position and contract type
                $kontrak->karyawan->jenis_kontrak = $jenisKontrakBaru->nama;
                $kontrak->karyawan->save();

                // Update karyawan_posisi pivot table if position changed
                if ($old_posisi_id !== $new_posisi_id) {
                    DB::table('karyawan_posisi')->where('karyawan_id', $kontrak->karyawan_id)->update(['posisi_id' => $new_posisi_id]);
                }
            }

            DB::commit();
            return redirect()->route('master-data.kontrak-detail.index')->with('success', 'Kontrak berhasil diperbarui!');
        } catch (Throwable $error) {
            DB::rollBack();
            Log::error("Error updating kontrak: " . $error->getMessage() . " Stack: " . $error->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui kontrak: ' . $error->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function delete(string $id)
    {
        DB::beginTransaction();
        try{
            $kontrak = Kontrak::find($id);
            $kontrak->delete();
            DB::commit();
            return response()->json(['message' => 'Kontrak Berhasil Dihapus!'], 200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function get_data_list_kontrak(string $karyawan_id)
    {
        $kontrak = Kontrak::where('karyawan_id', $karyawan_id)->orderBy('tanggal_selesai', 'DESC')->get();
        $list = [];
        if($kontrak){
            foreach($kontrak as $item){
                if($item->status == 'DONE'){
                    $badge = '<span class="badge badge-pill badge-success">'.$item->status.'</span>';
                } else {
                    $badge = '<span class="badge badge-pill badge-warning">'.$item->status.'</span>';
                }
                $list[] = [
                    'id_kontrak' => $item->id_kontrak,
                    'nama_posisi' => $item->nama_posisi,
                    'posisi_id' => $item->posisi_id,
                    'jenis' => $item->jenis,
                    'status' => $item->status,
                    'status_badge' => $badge,
                    'issued_date' => $item->issued_date,
                    'issued_date_text' => Carbon::parse($item->issued_date)->format('d M Y'),
                    'tempat_administrasi' => $item->tempat_administrasi,
                    'durasi' => $item->durasi,
                    'no_surat' => $item->no_surat,
                    'salary' => 'Rp. ' . number_format($item->salary, 0, ',', '.').' ,-',
                    'deskripsi' => $item->deskripsi,
                    'tanggal_mulai' => Carbon::parse($item->tanggal_mulai)->format('d M Y'),
                    'tanggal_selesai' => $item->tanggal_selesai !== null ? Carbon::parse($item->tanggal_selesai)->format('d M Y') : 'Unknown',
                    'attachment' => $item->attachment ? asset('storage/'.$item->attachment) : null
                ];
            }
            return response()->json(['data' => $list], 200);
        } else {
            return response()->json(['message' => 'Data Karyawan tidak ditemukan!'], 404);
        }

    }

    public function get_data_detail_kontrak(string $idKontrak)
    {
        $kontrak = Kontrak::find($idKontrak);
        $no_surat_numeric = substr($kontrak->no_surat, 4, 6 - 4 + 1);
        if($kontrak){
            $data = [
                'id_kontrak' => $kontrak->id_kontrak,
                'nama_karyawan' => $kontrak->karyawan->nama,
                'posisi_id' => $kontrak->posisi_id,
                'nama_posisi' => $kontrak->nama_posisi == $kontrak->posisi->nama ? '' : $kontrak->nama_posisi ,
                'jenis_kontrak_id' => $kontrak->jenis_kontrak_id,
                'status' => $kontrak->status,
                'issued_date' => $kontrak->issued_date,
                'tempat_administrasi' => $kontrak->tempat_administrasi,
                'durasi' => $kontrak->durasi,
                'no_surat' => $no_surat_numeric,
                'salary' => $kontrak->salary,
                'deskripsi' => $kontrak->deskripsi,
                'tanggal_mulai' => $kontrak->tanggal_mulai,
                'tanggal_selesai' => $kontrak->tanggal_selesai,
                'isReactive' => $kontrak->isReactive
            ];
            return response()->json(['data' => $data], 200);
        } else {
            return response()->json(['message' => 'Data Kontrak tidak ditemukan!'], 404);
        }
    }

    public function get_jenis_kontrak()
    {
        try {
            $jenisKontrak = JenisKontrak::all();
            return response()->json(['data' => $jenisKontrak], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function download_kontrak_kerja(string $idKontrak)
    {
        $organisasi_id = auth()->user()->organisasi_id;
        $kontrak = Kontrak::find($idKontrak);
        $template = Template::active()->organisasi($organisasi_id)->where('type', $kontrak->jenisKontrak->nama)->first();

        if($template){
            $templatePath = public_path('storage/'.$template->template_path);
        } else {
            $templateProcessor = new PhpWord();
            $section = $templateProcessor->addSection();
            $section->addText('Template not found');
            $objWriter = IOFactory::createWriter($templateProcessor, 'Word2007');
            header("Content-Disposition: attachment; filename=Template-not-found.docx");
            $objWriter->save('php://output');
        }
        $templateProcessor = new TemplateProcessor($templatePath);
        $tanggal_lahir = Carbon::parse($kontrak->karyawan->tanggal_lahir)->locale('id')->isoFormat('LL');
        $day = $this->get_nama_hari($kontrak->tanggal_mulai);
        $issued_date = Carbon::parse($kontrak->tanggal_mulai)->format('d/m/Y');
        $issued_date_format = Carbon::parse($kontrak->tanggal_mulai)->locale('id')->isoFormat('LL');
        $issued_date_text = $this->tanggal_to_kalimat($kontrak->tanggal_mulai);
        $tanggal_mulai = Carbon::parse($kontrak->tanggal_mulai)->format('d/m/Y');
        $tanggal_mulai_text = $this->tanggal_to_kalimat($kontrak->tanggal_mulai);

        if($kontrak->jenisKontrak->nama !== 'PKWTT'){
            $tanggal_selesai = Carbon::parse($kontrak->tanggal_selesai)->format('d/m/Y');
            $tanggal_selesai_text = $this->tanggal_to_kalimat($kontrak->tanggal_selesai);
        } else {
            $tanggal_selesai = null;
            $tanggal_selesai_text = null;
        }

        $durasi = $kontrak->durasi;
        $durasi_text = $this->angka_to_kata($durasi);
        $departemen = $kontrak->posisi->departemen->nama;
        $jabatan = $kontrak->posisi->jabatan->nama;
        $salary = $kontrak->salary;
        $salary_rupiah = 'Rp. ' . number_format($salary, 0, ',', '.').' ,-';
        $salary_text = $this->terbilang($salary).'Rupiah';
        $tempat_administrasi = $kontrak->tempat_administrasi;
        $year = Carbon::parse($kontrak->tanggal_mulai)->format('Y');

        $templateProcessor->setValue('nama', $kontrak->karyawan->nama);
        $templateProcessor->setValue('no_surat', $kontrak->no_surat);
        $templateProcessor->setValue('nik', $kontrak->karyawan->nik);
        $templateProcessor->setValue('tempat_lahir', $kontrak->karyawan->tempat_lahir);
        $templateProcessor->setValue('tempat_administrasi', $kontrak->tempat_administrasi);
        $templateProcessor->setValue('year', $year);
        $templateProcessor->setValue('jenis_kelamin', $kontrak->karyawan->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan');
        $templateProcessor->setValue('tanggal_lahir', $tanggal_lahir);
        $templateProcessor->setValue('alamat', $kontrak->karyawan->alamat);
        $templateProcessor->setValue('day', $day);
        $templateProcessor->setValue('issued_date', $issued_date);
        $templateProcessor->setValue('issued_date_text', $issued_date_text);
        $templateProcessor->setValue('issued_date_format', $issued_date_format);
        $templateProcessor->setValue('durasi', $durasi);
        $templateProcessor->setValue('durasi_text', $durasi_text);
        $templateProcessor->setValue('jabatan', $kontrak->posisi->jabatan->nama);
        $templateProcessor->setValue('departemen', $kontrak->posisi->jabatan->id_jabatan !== [1, 2] ? 'Departemen '.$kontrak->posisi->departemen->nama : 'Divisi '. $kontrak->posisi->divisi->nama);
        $templateProcessor->setValue('tanggal_mulai', $tanggal_mulai);
        $templateProcessor->setValue('tanggal_mulai_text', $tanggal_mulai_text);
        $templateProcessor->setValue('tanggal_selesai', $tanggal_selesai);
        $templateProcessor->setValue('tanggal_selesai_text', $tanggal_selesai_text);
        $templateProcessor->setValue('salary', $salary_rupiah);
        $templateProcessor->setValue('salary_text', $salary_text);

        header("Content-Disposition: attachment; filename=".$kontrak->id_kontrak.".docx");
        $templateProcessor->saveAs('php://output');
    }

    function get_nama_hari($tanggal) {
        $tanggal = Carbon::parse($tanggal)->format('Y-m-d');
        $date = Carbon::createFromFormat('Y-m-d', $tanggal);
        $namaHari = $date->locale('id')->isoFormat('dddd');

        return $namaHari;
    }

    function tanggal_to_kalimat($tanggal) {

        $tanggal = Carbon::parse($tanggal);
        $bulanIndonesia = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $hari = $tanggal->day;
        $bulan = $bulanIndonesia[$tanggal->month];
        $tahun = $tanggal->year;
        $tahun = substr($tahun, 2);

        $angkaKeKata = [
            '1' => 'Satu',
            '2' => 'Dua',
            '3' => 'Tiga',
            '4' => 'Empat',
            '5' => 'Lima',
            '6' => 'Enam',
            '7' => 'Tujuh',
            '8' => 'Delapan',
            '9' => 'Sembilan',
            '10' => 'Sepuluh',
            '11' => 'Sebelas',
            '12' => 'Dua Belas',
            '13' => 'Tiga Belas',
            '14' => 'Empat Belas',
            '15' => 'Lima Belas',
            '16' => 'Enam Belas',
            '17' => 'Tujuh Belas',
            '18' => 'Delapan Belas',
            '19' => 'Sembilan Belas',
            '20' => 'Dua Puluh',
            '21' => 'Dua Puluh Satu',
            '22' => 'Dua Puluh Dua',
            '23' => 'Dua Puluh Tiga',
            '24' => 'Dua Puluh Empat',
            '25' => 'Dua Puluh Lima',
            '26' => 'Dua Puluh Enam',
            '27' => 'Dua Puluh Tujuh',
            '28' => 'Dua Puluh Delapan',
            '29' => 'Dua Puluh Sembilan',
            '30' => 'Tiga Puluh',
            '31' => 'Tiga Puluh Satu',
        ];

        $hariKata = $angkaKeKata[$hari];
        $kalimatTanggal = $hariKata . ' ' . $bulan . ' ' . 'Dua Ribu ' . $angkaKeKata[$tahun];

        return $kalimatTanggal;
    }

    function angka_to_kata($angka) {

        $angkaKeKata = [
            '1' => 'Satu',
            '2' => 'Dua',
            '3' => 'Tiga',
            '4' => 'Empat',
            '5' => 'Lima',
            '6' => 'Enam',
            '7' => 'Tujuh',
            '8' => 'Delapan',
            '9' => 'Sembilan',
            '10' => 'Sepuluh',
            '11' => 'Sebelas',
            '12' => 'Dua Belas',
            '13' => 'Tiga Belas',
            '14' => 'Empat Belas',
            '15' => 'Lima Belas',
            '16' => 'Enam Belas',
            '17' => 'Tujuh Belas',
            '18' => 'Delapan Belas',
            '19' => 'Sembilan Belas',
            '20' => 'Dua Puluh',
            '21' => 'Dua Puluh Satu',
            '22' => 'Dua Puluh Dua',
            '23' => 'Dua Puluh Tiga',
            '24' => 'Dua Puluh Empat',
            '25' => 'Dua Puluh Lima',
            '26' => 'Dua Puluh Enam',
            '27' => 'Dua Puluh Tujuh',
            '28' => 'Dua Puluh Delapan',
            '29' => 'Dua Puluh Sembilan',
            '30' => 'Tiga Puluh',
            '31' => 'Tiga Puluh Satu',
        ];
        return $angkaKeKata[$angka];
    }

    function angka_to_rupiah_text($angka) {
        $angka = (int) $angka;

        $bilangan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $cabutan = ['', 'Ribu', 'Juta', 'Miliar', 'Triliun'];

        $nilai = '';
        if ($angka < 12) {
            $nilai = $bilangan[$angka];
        } else if ($angka < 20) {
            $nilai = $bilangan[$angka - 10] . ' Belas';
        } else {
            $i = 0;
            $baca = '';
            while ($angka >= 1000) {
                $sub_angka = $angka % 1000;
                $angka = intdiv($angka, 1000);
                $baca = $this->angka_to_rupiah_text($sub_angka) . ' ' . $cabutan[$i] . ' ';
                $i++;
            }

            if ($angka > 0 && $angka < 100) {
                if ($angka >= 10) {
                    $baca = $bilangan[$angka] . ' Ratus ' . $baca;
                } else {
                    $baca = $bilangan[$angka] . ' ' . $baca;
                }
            } else if ($angka > 0) {
                $baca = $bilangan[$angka] . ' ' . $baca;
            }

            $nilai = trim($baca);
        }

        return $nilai . ' Rupiah';
    }

    function terbilang($angka)
    {
        $angka = (int)$angka;
        $bilangan = array('', 'Satu ', 'Dua ', 'Tiga ', 'Empat ', 'Lima ', 'Enam ', 'Tujuh ', 'Delapan ', 'Sembilan ', 'Sepuluh ', 'Sebelas ');

        $temp = '';

        if ($angka < 12) {
            $temp = $bilangan[$angka];
        } else if ($angka < 20) {
            $temp = $bilangan[$angka - 10] . 'Belas ';
        } else if ($angka < 100) {
            $temp = self::terbilang($angka / 10) . 'Puluh ' . self::terbilang($angka % 10);
        } else if ($angka < 200) {
            $temp = 'Seratus' . self::terbilang($angka - 100);
        } else if ($angka < 1000) {
            $temp = self::terbilang($angka / 100) . 'Ratus ' . self::terbilang($angka % 100);
        } else if ($angka < 2000) {
            $temp = 'Seribu' . self::terbilang($angka - 1000);
        } else if ($angka < 1000000) {
            $temp = self::terbilang($angka / 1000) . 'Ribu ' . self::terbilang($angka % 1000);
        } else if ($angka < 1000000000) {
            $temp = self::terbilang($angka / 1000000) . 'Juta ' . self::terbilang($angka % 1000000);
        }

        return $temp;
    }

    public function upload_kontrak(Request $request, string $type, string $id_kontrak){
        $dataValidate = [
            'attachment' => ['file', 'max:5000', 'mimes:pdf'],
            'evidence' => ['file', 'max:5000', 'mimes:pdf'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            return response()->json(['message' => 'File harus bertipe PDF & Berukuran maksimal 5 mb.'], 402);
        }

        DB::beginTransaction();
        try {
            $kontrak = Kontrak::find($id_kontrak);

            if($type == 'attachment'){
                if($request->hasFile('attachment')){
                    $file = $request->file('attachment');
                    $kontrak_scan = $kontrak->karyawan->nama . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $file_path = $file->storeAs("attachment/kontrak", $kontrak_scan);
                    if($kontrak->attachment)
                    {
                        Storage::delete($kontrak->attachment);
                    }
                    $kontrak->attachment = $file_path;
                    $kontrak->save();
                }
            } else {
                if($request->hasFile('evidence')){
                    $file = $request->file('evidence');
                    $evidence = $kontrak->karyawan->nama . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $file_path = $file->storeAs("attachment/evidence", $evidence);
                    if($kontrak->evidence)
                    {
                        Storage::delete($kontrak->evidence);
                    }
                    $kontrak->evidence = $file_path;
                    $kontrak->save();
                }
            }

            DB::commit();
            return response()->json(['message' => 'Upload File pada '.$id_kontrak.' Sukses!'],200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function done_kontrak(Request $request, string $id_kontrak)
    {
        DB::beginTransaction();
        try{

            $isReactive = $request->isReactive;
            $kontrak = Kontrak::find($id_kontrak);
            $karyawan = Karyawan::find($kontrak->karyawan_id);

            //cek apakah file evidence & attachment sudah diupload
            if($kontrak->evidence == null || $kontrak->attachment == null)
            {
                DB::commit();
                return response()->json(['message' => 'Upload File Evidence & Attachment terlebih dahulu!'], 402);
            }

            //update tanggal selesai karyawan
            if ($isReactive == 'Y') {
                $karyawan->tanggal_mulai = $kontrak->tanggal_mulai;
                $karyawan->tanggal_selesai = $kontrak->tanggal_selesai;
            } else {
                // Fix: Gunakan query builder yang benar untuk mengecek kondisi karyawan baru
                $new_data = Karyawan::where('id_karyawan', $karyawan->id_karyawan)
                    ->whereNotNull('tanggal_mulai')
                    ->whereNull('tanggal_selesai')
                    ->whereNull('jenis_kontrak')
                    ->exists();

                if($new_data){
                    // KONDISI JIKA KARYAWAN BARU DAN KONTRAK
                    if($kontrak->jenisKontrak->nama !== 'PKWTT' && $kontrak->jenisKontrak->nama !== 'PENGKARYAAN'){
                        $existingCB = Event::whereDate('tanggal_mulai', '<=', $kontrak->tanggal_selesai)->where('jenis_event', 'CB');
                    } else {
                        $existingCB = Event::whereDate('tanggal_mulai', '>=', $kontrak->tanggal_mulai)->where('jenis_event', 'CB');
                    }

                    if($existingCB->exists()){
                        try {
                            foreach($existingCB->get() as $cutiBersama){
                                $jatah_cuti_bersama = $karyawan->sisa_cuti_bersama - $cutiBersama->durasi;
                                if($jatah_cuti_bersama >= 0){
                                    $karyawan->sisa_cuti_bersama = $jatah_cuti_bersama;
                                } else {
                                    $karyawan->sisa_cuti_bersama = 0;
                                    $karyawan->hutang_cuti = abs($jatah_cuti_bersama);
                                }
                            }
                            $karyawan->save();
                        } catch (Throwable $e) {
                            // Log error tapi tetap lanjutkan update tanggal_selesai
                            Log::warning('Error updating cuti bersama: ' . $e->getMessage());
                        }
                    }
                } else {
                    // KONDISI KARYAWAN SUDAH ADA KONTRAK SEBELUMNYA
                    if($kontrak->jenisKontrak->nama !== 'PKWTT' && $kontrak->jenisKontrak->nama !== 'PENGKARYAAN'){
                        $existingCutiBersama = Event::whereDate('tanggal_mulai', '<=', $kontrak->tanggal_selesai)->where('jenis_event', 'CB');
                    } else {
                        $existingCutiBersama = Event::whereDate('tanggal_mulai', '>=', $kontrak->tanggal_mulai)->where('jenis_event', 'CB');
                    }

                    if($existingCutiBersama->exists()){
                        try {
                            foreach($existingCutiBersama->get() as $cutiBersama){
                                // CEK APAKAH ADA KONTRAK YANG MEMILIKI TANGGAL SELESAI LEBIH DARI CUTI BERSAMA
                                $existingKontrak = Kontrak::where('karyawan_id', $karyawan->id_karyawan)
                                    ->where('status', 'DONE')
                                    ->where('tanggal_selesai', '>=', $cutiBersama->tanggal_mulai)
                                    ->exists();

                                // JIKA BELUM ADA KONTRAK SEBELUMNYA, KURANGI SISA CUTI BERSAMA
                                if(!$existingKontrak){
                                    $jatah_cuti_bersama = $karyawan->sisa_cuti_bersama - $cutiBersama->durasi;
                                    if($jatah_cuti_bersama >= 0){
                                        $karyawan->sisa_cuti_bersama = $jatah_cuti_bersama;
                                    } else {
                                        $karyawan->sisa_cuti_bersama = 0;
                                        $karyawan->hutang_cuti = abs($jatah_cuti_bersama);
                                    }
                                }
                            }
                            $karyawan->save();
                        } catch (Throwable $e) {
                            // Log error tapi tetap lanjutkan update tanggal_selesai
                            Log::warning('Error updating cuti bersama existing kontrak: ' . $e->getMessage());
                        }
                    }
                }

                // PASTIKAN TANGGAL SELESAI SELALU TER-UPDATE TERLEPAS DARI ERROR CUTI BERSAMA
                if($kontrak->jenisKontrak->nama == 'PKWTT' || $kontrak->jenisKontrak->nama == 'PENGKARYAAN'){
                    $karyawan->tanggal_selesai = null;
                } else {
                    $karyawan->tanggal_selesai = $kontrak->tanggal_selesai;
                }
            }

            //update status kontrak
            $kontrak->status = 'DONE';
            $kontrak->save();

            $kontrak->load('jenisKontrak'); // Eager load the relationship
            $karyawan->jenis_kontrak = $kontrak->jenisKontrak->nama ?? null; // Use the name from the relationship
            $karyawan->status_karyawan = 'AT';
            $karyawan->save();

            DB::commit();
            return response()->json(['message' => 'Kontrak Berhasil Selesai!'], 200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function upload_data_kontrak(Request $request)
    {
        $dataValidate = [
            'kontrak_file' => ['required', 'file', 'mimes:xlsx,xls'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        try {
            $organisasi_id = auth()->user()->organisasi_id;
            $user = auth()->user();

            if($request->hasFile('kontrak_file')){
                $file = $request->file('kontrak_file');
                $kontrak_records = 'KN_' . time() . '.' . $file->getClientOriginalExtension();
                $kontrak_file = $file->storeAs("attachment/upload-kontrak", $kontrak_records);
            }

           if (file_exists(storage_path("app/public/".$kontrak_file))) {
                $spreadsheet = PhpSpreadsheetIOFactory::load(storage_path("app/public/".$kontrak_file));
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();
                $dataWithoutHeader = array_slice($data, 1);

                if (count($dataWithoutHeader) < 1) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Data tidak ditemukan!'
                    ], 404);
                }

                UploadKontrakJob::dispatch($dataWithoutHeader, $organisasi_id, $user);
                return response()->json([
                    'status' => 'success',
                    'message' => 'File uploaded successfully, please wait for the process to finish (job)',
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please upload a file',
                ], 500);
            }

        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function upload_datatable(Request $request)
    {

        $columns = array(
            0 => 'activity_log.description',
            1 => 'users.username',
            2 => 'activity_log.created_at',
        );

        $totalData = ActivityLog::count();
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

        $dataFilter['log_name'] = 'error_job_upload_kontrak';

        $uploadLog = ActivityLog::getData($dataFilter, $settings);
        $totalFiltered = ActivityLog::countData($dataFilter);

        $dataTable = [];

        if (!empty($uploadLog)) {
            foreach ($uploadLog as $data) {
                $nestedData['description'] = $data->description;
                $nestedData['causer'] = $data->username;
                $nestedData['created_at'] = Carbon::parse($data->created_at)->translatedFormat('d F Y H:i:s');
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

    // public function upload_data_kontrak(Request $request)
    // {
    //     $file = $request->file('kontrak_file');
    //     $organisasi_id = auth()->user()->organisasi_id;
    //     $tempat_administrasi = auth()->user()->organisasi->nama;

    //     $validator = Validator::make($request->all(), [
    //         'kontrak_file' => 'required|mimes:xlsx,xls'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['message' => 'File Harus bertipe Excel!'], 400);
    //     }

    //     DB::beginTransaction();
    //     try {

    //         if($request->hasFile('kontrak_file')){
    //             $kontrak_records = 'KN_' . time() . '.' . $file->getClientOriginalExtension();
    //             $kontrak_file = $file->storeAs("attachment/upload-kontrak", $kontrak_records);
    //         }

    //         if (file_exists(storage_path("app/public/".$kontrak_file))) {
    //             $spreadsheet = PhpSpreadsheetIOFactory::load(storage_path("app/public/".$kontrak_file));
    //             $worksheet = $spreadsheet->getActiveSheet();
    //             $data = $worksheet->toArray();
    //             $karyawans = [];

    //             foreach ($data as $index => $row) {
    //                 if ($index == 0) continue;

    //                 activity('upload_kontrak_karyawan')->log('insert row ' . $index);
    //                 $karyawan_exist = Karyawan::where('ni_karyawan', $row[0])->organisasi(auth()->user()->organisasi_id);
    //                 if($karyawan_exist->exists()){
    //                     $karyawan = $karyawan_exist->first();
    //                     $karyawans[] = $karyawan->id_karyawan;
    //                 } else {
    //                     return response()->json(['message' => 'Karyawan dengan Nomor Induk '.$row[0].' tidak ditemukan!'], 404);
    //                 }

    //                 //Convert tanggal mulai dan selesai ke format Ymd jika ada
    //                 if($row[7] !== null){
    //                     try {
    //                         $tanggal_mulai = Carbon::createFromFormat('d/m/Y', $row[7])->format('Y-m-d');
    //                     }
    //                     catch (Exception $e) {
    //                         return response()->json(['message' => 'Format tanggal mulai salah!'], 402);
    //                     }
    //                 }

    //                 if($row[4] !== 'PKWTT'){
    //                     if($row[8] !== null){
    //                         try {
    //                             $tanggal_selesai = Carbon::createFromFormat('d/m/Y', $row[8])->format('Y-m-d');
    //                         }
    //                         catch (Exception $e) {
    //                             return response()->json(['message' => 'Format tanggal selesai salah!'], 402);
    //                         }
    //                     }
    //                 } else {
    //                     $tanggal_selesai = null;
    //                 }

    //                 //Validasi Kolom Numeric
    //                 if (!is_numeric($row[2]) || !is_numeric($row[5]) || $row[5] < 0 || !is_numeric($row[6]) || $row[6] < 0) {
    //                     return response()->json(['message' => 'Kolom ID Posisi, Durasi dan Salary harus berupa numeric!'], 402);
    //                 }

    //                 $posisi_id = Posisi::where('id_posisi', $row[2])->first()->id_posisi;
    //                 if($posisi_id == null){
    //                     return response()->json(['message' => 'Posisi dengan ID '.$row[2].' tidak ditemukan!'], 404);
    //                 }

    //                 //Validasi Jenis Kontrak
    //                 if (!in_array($row[4], ['PKWT', 'PKWTT', 'MAGANG'])) {
    //                     return response()->json(['message' => 'Jenis kontrak pada baris ' . ($index + 1) . ' harus PKWT, PKWTT, atau MAGANG!'], 402);
    //                 }

    //                 //Validasi Nomor Induk Karyawan
    //                 $karyawan = Karyawan::where('ni_karyawan', $row[0])->first();
    //                 if($karyawan->id_karyawan == null){
    //                     return response()->json(['message' => 'Karyawan dengan Nomor Induk '.$row[0].' tidak ditemukan!'], 404);
    //                 }

    //                 //Validasi Tempat Administrasi
    //                 if (!in_array($row[9], ['Karawang', 'Purwakarta'])) {
    //                     return response()->json(['message' => 'Tempat administasi tidak sesuai format!'], 402);
    //                 }

    //                 //Update data karyawan
    //                 $karyawan->jenis_kontrak = $row[4];
    //                 $karyawan->status_karyawan = 'AT';

    //                 if($row[4] !== 'PKWTT'){
    //                     if($karyawan->tanggal_selesai < $tanggal_selesai || $karyawan->tanggal_selesai == null){
    //                         $karyawan->tanggal_selesai = $tanggal_selesai;
    //                     }
    //                 } else {
    //                     $karyawan->tanggal_selesai = null;
    //                 }

    //                 $karyawan->save();

    //                 // Input Kontrak
    //                 Kontrak::create([
    //                     'no_surat' => $row[3],
    //                     'id_kontrak' => 'KONTRAK-'. Str::random(4) . '-' . now()->timestamp,
    //                     'karyawan_id' =>  $karyawan->id_karyawan,
    //                     'posisi_id' => $posisi_id,
    //                     'nama_posisi' => Posisi::find($posisi_id)->nama ? Posisi::find($posisi_id)->nama : '',
    //                     'jenis' => $row[4],
    //                     'status' => 'DONE',
    //                     'durasi' => $row[4] !== 'PKWTT' ? $row[5] : null,
    //                     'salary' => $row[6],
    //                     'deskripsi' => 'History Kontrak Karyawan',
    //                     'tanggal_mulai' => $tanggal_mulai,
    //                     'tanggal_selesai' => $row[4] !== 'PKWTT' ? $tanggal_selesai : null,
    //                     'tempat_administrasi' => $row[9],
    //                     'isReactive' => 'N',
    //                     'organisasi_id' => $organisasi_id,
    //                     'issued_date' => Carbon::now()->format('Y-m-d'),
    //                 ]);
    //             }

    //             //Update sisa cuti bersama karyawan
    //             $array_karyawan = array_unique($karyawans);
    //             foreach ($array_karyawan as $index => $kry){
    //                 $k = Karyawan::find($kry);
    //                 //CEK APAKAH ADA CUTI BERSAMA SEBELUM TANGGAL SELESAI KONTRAK YANG BARU DI UPLOAD
    //                 if($k && $k->tanggal_selesai !== null && $k->jenis_kontrak !== 'PKWTT'){
    //                     $kontrak = Kontrak::where('karyawan_id', $kry)->where('status', 'DONE')->orderBy('tanggal_selesai', 'DESC')->first();
    //                     $existingCB = Event::whereDate('tanggal_mulai', '<=', $kontrak->tanggal_selesai)->where('jenis_event', 'CB');
    //                     if($existingCB->exists()){
    //                         foreach($existingCB->get() as $cutiBersama){
    //                             $jatah_cuti_bersama = $k->sisa_cuti_bersama - $cutiBersama->durasi;
    //                             if($jatah_cuti_bersama >= 0){
    //                                 $k->sisa_cuti_bersama = $jatah_cuti_bersama;
    //                                 $k->save();
    //                             } else {
    //                                 $k->sisa_cuti_bersama = 0;
    //                                 $k->hutang_cuti = abs($jatah_cuti_bersama);
    //                                 $k->save();
    //                             }
    //                         }
    //                     }
    //                 } elseif ($k && $k->jenis_kontrak == 'PKWTT'){
    //                     $kontrak = Kontrak::where('karyawan_id', $kry)->where('status', 'DONE')->where('jenis', 'PKWTT')->orderBy('tanggal_mulai', 'DESC')->first();
    //                     $tanggal_selesai_temp = Carbon::now()->year.'-'.Carbon::parse($kontrak->tanggal_mulai)->format('m-d');
    //                     $existingCB = Event::whereDate('tanggal_mulai', '>=', $tanggal_selesai_temp)->where('jenis_event', 'CB');
    //                     if($existingCB->exists()){
    //                         foreach($existingCB->get() as $cutiBersama){
    //                             $jatah_cuti_bersama = $k->sisa_cuti_bersama - $cutiBersama->durasi;
    //                             if($jatah_cuti_bersama >= 0){
    //                                 $k->sisa_cuti_bersama = $jatah_cuti_bersama;
    //                                 $k->save();
    //                             } else {
    //                                 $k->sisa_cuti_bersama = 0;
    //                                 $k->hutang_cuti = abs($jatah_cuti_bersama);
    //                                 $k->save();
    //                             }
    //                         }
    //                     }
    //                 }
    //             }
    //         } else {
    //             DB::rollBack();
    //             return response()->json(['message' => 'Terjadi kesalahan, silahkan upload ulang file!'], 404);
    //         }
    //         DB::commit();
    //         return response()->json(['message' => 'File berhasil di upload'], 200);
    //     } catch (Throwable $e) {
    //         DB::rollBack();
    //         return response()->json(['message' => 'Error processing the file: ' . $e->getMessage()], 500);
    //     }
    // }

    public function ringkasanKontrak()
    {
        $dataPage = [
            'pageTitle' => "Master Data - Detail Kontrak",
            'page' => 'masterdata-kontrak-detail',
        ];
        return view('pages.master-data.kontrak.ringkasan', $dataPage);
    }

    public function datatableRingkasanKontrak(Request $request)
    {
        try { // Wrap the entire method in a try-catch block
            $columns = array(
                0 => 'karyawans.ni_karyawan',
                1 => 'karyawans.nama',
                2 => 'karyawans.tanggal_mulai',
                3 => 'departemens.nama',
                4 => 'karyawans.jenis_kontrak',
                5 => 'jumlah_kontrak', // This is an alias, might need special handling for orderBy
                6 => 'karyawans.tanggal_selesai',
            );

        $totalData = Karyawan::where('status_karyawan', '=', 'AT')->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = (!empty($request->input('order.0.column'))) ? $columns[$request->input('order.0.column')] : $columns[0];
        $dir = (!empty($request->input('order.0.dir'))) ? $request->input('order.0.dir') : "DESC";

        $karyawanQuery = Karyawan::select(
            'karyawans.id_karyawan',
            'karyawans.ni_karyawan',
            'karyawans.nama',
            'karyawans.tanggal_mulai',
            'departemens.nama as nama_departemen',
            'karyawans.jenis_kontrak',
            DB::raw('(SELECT COUNT(*) FROM kontraks WHERE kontraks.karyawan_id = karyawans.id_karyawan) as jumlah_kontrak'),
            DB::raw('(SELECT id_kontrak FROM kontraks WHERE kontraks.karyawan_id = karyawans.id_karyawan ORDER BY tanggal_mulai DESC LIMIT 1) as id_kontrak'),
            'karyawans.tanggal_selesai'
        )
        ->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', '=', 'karyawan_posisi.karyawan_id')
        ->leftJoin('posisis', 'karyawan_posisi.posisi_id', '=', 'posisis.id_posisi')
        ->leftJoin('departemens', 'posisis.departemen_id', '=', 'departemens.id_departemen')
        ->where('karyawans.status_karyawan', '=', 'AT')
        ->groupBy('karyawans.id_karyawan', 'departemens.nama');

        $search = $request->input('search.value');
        if (!empty($search)) {
            $karyawanQuery->where(function($q) use ($search){
                $q->where('karyawans.ni_karyawan','LIKE',"%{$search}%"
                )->orWhere('karyawans.nama','LIKE',"%{$search}%"
                )->orWhere('departemens.nama','LIKE',"%{$search}%"
                )->orWhere('karyawans.jenis_kontrak','LIKE',"%{$search}%"
                );
            });
        }

        $totalFiltered = $karyawanQuery->get()->count();

        // Special handling for ordering by 'jumlah_kontrak' if it's an alias
            if ($order === 'jumlah_kontrak') {
                $karyawans = $karyawanQuery->offset($start)
                    ->limit($limit)
                    ->orderBy(DB::raw('jumlah_kontrak'), $dir) // Order by the raw alias
                    ->get();
            } else {
                $karyawans = $karyawanQuery->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
            }

        $dataTable = [];
        if (!empty($karyawans)) {
            foreach ($karyawans as $data) {
                try { // Add try-catch block here
                    $lama_kerja = '-';
                    if ($data->tanggal_mulai) {
                        $tanggal_mulai = \Carbon\Carbon::parse($data->tanggal_mulai);
                        $lama_kerja = $tanggal_mulai->diff(\Carbon\Carbon::now())->format('%y tahun %m bulan %d hari');
                    }

                    $currentPosisi = DB::table('karyawan_posisi')
                        ->where('karyawan_id', $data->id_karyawan)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    $penetapanJabatan = null;
                    if ($currentPosisi) {
                        $penetapanJabatan = Kontrak::where('karyawan_id', $data->id_karyawan)
                            ->where('posisi_id', $currentPosisi->posisi_id)
                            ->where('status', 'DONE')
                            ->orderBy('tanggal_mulai', 'desc')
                            ->first();
                    }

                    $penetapanKartap = Kontrak::where('kontraks.karyawan_id', $data->id_karyawan)
                        ->join('jenis_kontraks', 'kontraks.jenis_kontrak_id', '=', 'jenis_kontraks.id')
                        ->where('jenis_kontraks.nama', 'PKWTT')
                        ->where('kontraks.status', 'DONE')
                        ->orderBy('kontraks.tanggal_mulai', 'asc')
                        ->select('kontraks.*') // Avoid ambiguity
                        ->first();

                    $nestedData['ni_karyawan'] = $data->ni_karyawan;
                    $nestedData['nama_karyawan'] = $data->nama;
                    $nestedData['tanggal_masuk'] = Carbon::parse($data->tanggal_mulai)->format('d M Y');
                    $nestedData['dept'] = $data->nama_departemen ?? '-'; // Handle null departemen
                    $nestedData['tanggal_penetapan_jabatan'] = $penetapanJabatan ? Carbon::parse($penetapanJabatan->tanggal_mulai)->format('d M Y') : '-';
                    $nestedData['tanggal_penetapan_kartap'] = $penetapanKartap ? Carbon::parse($penetapanKartap->tanggal_mulai)->format('d M Y') : '-';
                    $nestedData['lama_kerja'] = $lama_kerja;
                    $nestedData['jenis_kontrak'] = $data->jenis_kontrak;
                    $nestedData['jumlah_kontrak'] = 'K'.$data->jumlah_kontrak;
                    $nestedData['tanggal_berakhir'] = $data->tanggal_selesai ? Carbon::parse($data->tanggal_selesai)->format('d M Y') : '-'; // Handle null tanggal_selesai
                    $actionButtons = '<a href="'.route('masterdata.kontrak.detail', $data->id_karyawan).'" class="btn btn-sm btn-info">Detail</a>';

                    $nestedData['action'] = $actionButtons;
                    $dataTable[] = $nestedData;
                } catch (\Exception $e) {
                    Log::error("Error processing Karyawan data for DataTables: " . $e->getMessage(), ['karyawan_id' => $data->id_karyawan ?? 'unknown']);
                    // Optionally, add a placeholder row or skip this row
                    $nestedData['ni_karyawan'] = $data->ni_karyawan ?? 'Error';
                    $nestedData['nama_karyawan'] = $data->nama ?? 'Error';
                    $nestedData['tanggal_masuk'] = 'Error';
                    $nestedData['dept'] = 'Error';
                    $nestedData['tanggal_penetapan_jabatan'] = 'Error';
                    $nestedData['tanggal_penetapan_kartap'] = 'Error';
                    $nestedData['lama_kerja'] = 'Error';
                    $nestedData['jenis_kontrak'] = 'Error';
                    $nestedData['jumlah_kontrak'] = 'Error';
                    $nestedData['tanggal_berakhir'] = 'Error';
                    $nestedData['action'] = '<span class="text-danger">Data Error</span>';
                    $dataTable[] = $nestedData;
                }
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $dataTable
        );

        return response()->json($json_data, 200);

        } catch (\Throwable $e) {
            Log::error("Fatal error in datatableRingkasanKontrak: " . $e->getMessage() . " Stack: " . $e->getTraceAsString());
            return response()->json([
                "draw" => intval($request->input('draw')),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
                "error" => "An internal server error occurred. Please check logs for details."
            ], 500);
        }
    }

    public function detailKontrak($id)
    {
        $karyawan = Karyawan::find($id);

        if (!$karyawan) {
            Log::warning("Karyawan with ID {$id} not found for detailKontrak.");
            return redirect()->route('master-data.kontrak-detail.index')->with('error', 'Karyawan tidak ditemukan.');
        }

        $kontraks = Kontrak::where('karyawan_id', $id)->orderBy('tanggal_mulai', 'asc')->get();

        // Add recommendation logic
        foreach ($kontraks as $index => $kontrak) {
            $recommendation = '';
            if ($kontrak->status == 'DONE') {
                if (isset($kontraks[$index + 1])) {
                    $nextKontrak = $kontraks[$index + 1];
                    if ($nextKontrak->status == 'DONE') {

                        $isCurrentPkwtt = ($kontrak->jenisKontrak->nama === 'PKWTT' || $kontrak->jenisKontrak->nama === 'PENGKARYAAN');
                        $isNextPkwtt = ($nextKontrak->jenisKontrak->nama === 'PKWTT' || $nextKontrak->jenisKontrak->nama === 'PENGKARYAAN');
                        $isPositionChanged = ($kontrak->posisi_id !== $nextKontrak->posisi_id);

                        if ($isCurrentPkwtt && $isNextPkwtt && $isPositionChanged) {
                            $recommendation = 'naik jabatan';
                        } else if ($nextKontrak->jenisKontrak->nama !== 'PKWTT' && $nextKontrak->jenisKontrak->nama !== 'PENGKARYAAN') {
                            $recommendation = 'di perpanjang';
                        } else {
                            if (!$isCurrentPkwtt) {
                                $recommendation = 'diangkat';
                            }
                        }
                    }
                } else {
                    // Last contract
                    if ($kontrak->tanggal_selesai && \Carbon\Carbon::parse($kontrak->tanggal_selesai)->isPast()) {
                        $recommendation = 'habis kontrak';
                    }
                }
            }
            $kontrak->status_rekomendasi = $recommendation;
        }

        Log::info("Detail Kontrak for Karyawan ID: {$id}", ['karyawan' => $karyawan->toArray(), 'kontraks_count' => $kontraks->count()]);

        $dataPage = [
            'pageTitle' => "Master Data - Detail Kontrak",
            'page' => 'masterdata-kontrak-detail',
            'karyawan' => $karyawan,
            'kontraks' => $kontraks
        ];

        return view('pages.master-data.kontrak.detail', $dataPage);
    }

    public function endKontrak(Request $request, string $id_kontrak)
    {
        DB::beginTransaction();
        try {
            $kontrak = Kontrak::find($id_kontrak);

            if (!$kontrak) {
                return response()->json(['message' => 'Kontrak tidak ditemukan!'], 404);
            }

            // Set tanggal_selesai to today if not already set or if it's in the future
            if (!$kontrak->tanggal_selesai || Carbon::parse($kontrak->tanggal_selesai)->isFuture()) {
                $kontrak->tanggal_selesai = Carbon::now()->format('Y-m-d');
            }
            $kontrak->status = 'DONE'; // Mark as done
            $kontrak->save();

            // Update karyawan's overall tanggal_selesai if this was their latest contract
            $karyawan = Karyawan::find($kontrak->karyawan_id);
            if ($karyawan && $karyawan->tanggal_selesai == $kontrak->tanggal_selesai) {
                // This logic might need refinement based on how 'latest contract' is determined for Karyawan
                // For now, we assume if the dates match, it's the latest.
                $karyawan->tanggal_selesai = $kontrak->tanggal_selesai;
                $karyawan->save();
            }

            DB::commit();
            return response()->json(['message' => 'Kontrak berhasil diakhiri!'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Error ending kontrak: " . $e->getMessage() . " Stack: " . $e->getTraceAsString());
            return response()->json(['message' => 'Terjadi kesalahan saat mengakhiri kontrak.'], 500);
        }
    }

    public function editKontrakForm(string $id_kontrak)
    {
        $kontrak = Kontrak::with(['karyawan', 'jenisKontrak', 'posisi'])->find($id_kontrak);
        if (!$kontrak) {
            return redirect()->route('master-data.kontrak-detail.index')->with('error', 'Kontrak tidak ditemukan!');
        }
        $posisi = Posisi::all();
        $jenisKontrak = JenisKontrak::all();

    $dataPage = [
        'pageTitle' => "Edit Kontrak",
        'page' => 'masterdata-kontrak',
        'kontrak' => $kontrak,
        'posisi' => $posisi,
        'jenisKontrak' => $jenisKontrak
    ];

        return view('pages.master-data.kontrak.edit', $dataPage);
    }
}
