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
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpSpreadsheet\IOFactory as PhpSpreadsheetIOFactory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
            0 => 'id_kontrak',
            1 => 'karyawans.nama',
            2 => 'departemens.nama',
            3 => 'kontraks.nama_posisi',
            4 => 'no_surat',
            5 => 'issued_date',
            6 => 'jenis',
            7 => 'status',
            8 => 'durasi',
            9 => 'salary',
            10 => 'tanggal_mulai',
            11 => 'tanggal_selesai',
        );

        $totalData = Kontrak::count();
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
        $jenis = $request->jenis;
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

            if($jenis !== 'PKWTT'){
                
                if ($durasi == 0) {
                    DB::commit();
                    return response()->json(['message' => 'Durasi tidak boleh kosong!'], 402);
                }

                $no_surat_int = intval($no_surat);
                foreach ($karyawan_id as $karyawan) {
                    $kry = Karyawan::find($karyawan);
                    $kontrak_karyawan = $kry->kontrak()->where('status', 'DONE')->count() + 1;

                    //No Surat Text
                    $kry->jenis_kontrak = $jenis;
                    $bulan_romawi = $this->angka_to_romawi(Carbon::parse($tanggal_mulai)->month);
                    $hrd = $tempat_administrasi == 'Karawang' ? 'HRD-TCF3' : 'HRD-TCF2';
                    $jenis_on_surat = ($jenis == 'MAGANG' ? 'MG' : $jenis).($jenis == 'PKWT' || $jenis == 'MAGANG' ? '-'.$this->angka_to_romawi($kontrak_karyawan) : '');
                    $tahun = Carbon::parse($tanggal_mulai)->format('Y');
                    $no_surat_text = 'No. ' . str_pad($no_surat_int, 3, '0', STR_PAD_LEFT) . '/' . $jenis_on_surat . '/' . $hrd . '/'.$bulan_romawi.'/' . $tahun;

                    $kry->save();
                    $kontrak = Kontrak::create([
                        'id_kontrak' => 'KONTRAK-'. Str::random(4) . '-' . (now()->timestamp + 1),
                        'karyawan_id' => $karyawan,
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
                    $kry->jenis_kontrak = $jenis;
                    $bulan_romawi = $this->angka_to_romawi(Carbon::parse($tanggal_mulai)->month);
                    $hrd = $tempat_administrasi == 'Karawang' ? 'HRD-TCF3' : 'HRD-TCF2';
                    $jenis_on_surat = $jenis.'-'.$this->angka_to_romawi($kontrak_karyawan);
                    $tahun = Carbon::parse($tanggal_mulai)->format('Y');
                    $no_surat_text = 'No. ' . str_pad($no_surat_int, 3, '0', STR_PAD_LEFT) . '/' . $jenis_on_surat . '/' . $hrd . '/'.$bulan_romawi.'/' . $tahun;
                    $kry->save();
                    $kontrak = Kontrak::create([
                        'id_kontrak' => 'KONTRAK-'. Str::random(4) . '-' . now()->timestamp,
                        'karyawan_id' => $karyawan,
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
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Database error: ' . $e->getMessage()], 500);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Model not found: ' . $e->getMessage()], 404);
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
            'no_surat_kontrakEdit' => ['required','digits:3'],
            'issued_date_kontrakEdit' => ['required','date'],   
            'tempat_administrasi_kontrakEdit' => ['required'],
            'durasi_kontrakEdit' => ['numeric','nullable'],
            'salary_kontrakEdit' => ['numeric','required'],
            'tanggal_mulai_kontrakEdit' => ['required','date'],
            'tanggal_selesai_kontrakEdit' => ['nullable','date'],
            'jenis_kontrakEdit' => ['required'],
            'posisi_kontrakEdit' => ['required'], 
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $no_surat = $request->no_surat_kontrakEdit;
        $issued_date = $request->issued_date_kontrakEdit;
        $tempat_administrasi = $request->tempat_administrasi_kontrakEdit;
        $durasi = $request->durasi_kontrakEdit; 
        $salary = $request->salary_kontrakEdit;
        $tanggal_mulai = $request->tanggal_mulai_kontrakEdit;
        $tanggal_selesai = $request->tanggal_selesai_kontrakEdit;
        $jenis = $request->jenis_kontrakEdit;
        $posisi = $request->posisi_kontrakEdit; 
        $nama_posisi = $request->nama_posisi_kontrakEdit;
        $deskripsi = $request->deskripsi_kontrakEdit;
        $organisasi_id = auth()->user()->organisasi_id;

        DB::beginTransaction();
        try{

            if ($jenis !== 'PKWTT' && $durasi == 0){
                DB::commit();
                return response()->json(['message' => 'Durasi tidak boleh kosong!'], 402);
            }
            

            $kontrak = Kontrak::find($id_kontrak);
            $kontrak_karyawan = Kontrak::where('karyawan_id',$kontrak->karyawan_id)->where('status', 'DONE')->count() + 1;

            //No Surat Text
            $bulan_romawi = $this->angka_to_romawi(Carbon::parse($tanggal_mulai)->month);
            $hrd = $tempat_administrasi == 'Karawang' ? 'HRD-TCF3' : 'HRD-TCF2';
            $jenis_on_surat = ($jenis == 'MAGANG' ? 'MG' : $jenis).($jenis == 'PKWT' || $jenis == 'MAGANG' ? '-'.$this->angka_to_romawi($kontrak_karyawan) : '');
            $tahun = Carbon::parse($tanggal_mulai)->format('Y');
            $no_surat_text = 'No. ' . str_pad($no_surat, 3, '0', STR_PAD_LEFT) . '/' . $jenis_on_surat . '/' . $hrd . '/'.$bulan_romawi.'/' . $tahun;
            
            $kontrak->no_surat = $no_surat_text;
            $kontrak->issued_date = $issued_date;   
            $kontrak->tempat_administrasi = $tempat_administrasi;

            $kry = Karyawan::find($kontrak->karyawan_id);
            $kry->jenis_kontrak = $jenis;
            $kry->save();

            if($jenis == 'PKWTT'){
                $durasi = null;
                $tanggal_selesai = null;
            } 
            
            $kontrak->organisasi_id = $organisasi_id;
            $kontrak->durasi = $durasi;
            $kontrak->salary = $salary;
            $kontrak->tanggal_mulai = $tanggal_mulai;
            $kontrak->tanggal_selesai = $tanggal_selesai;
            $kontrak->jenis = $jenis;
            $kontrak->posisi_id = $posisi;
            $kontrak->nama_posisi = $nama_posisi !== Posisi::find($posisi)->nama ? $nama_posisi : Posisi::find($posisi)->nama;
            $kontrak->deskripsi = $deskripsi;

            $kontrak->save();

            DB::commit();
            return response()->json(['message' => 'Kontrak Diupdate!'],200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
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
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Database error: ' . $e->getMessage()], 500);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Model not found: ' . $e->getMessage()], 404);
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
                'jenis' => $kontrak->jenis,
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

    public function download_kontrak_kerja(string $idKontrak)
    {
        $organisasi_id = auth()->user()->organisasi_id;
        $kontrak = Kontrak::find($idKontrak);
        $template = Template::active()->organisasi($organisasi_id)->where('type', $kontrak->jenis)->first();

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
        $day = $this->get_nama_hari($kontrak->issued_date);
        $issued_date = Carbon::parse($kontrak->issued_date)->format('d/m/Y');
        $issued_date_format = Carbon::parse($kontrak->issued_date)->locale('id')->isoFormat('LL');
        $issued_date_text = $this->tanggal_to_kalimat($kontrak->issued_date);
        $tanggal_mulai = Carbon::parse($kontrak->tanggal_mulai)->format('d/m/Y');
        $tanggal_mulai_text = $this->tanggal_to_kalimat($kontrak->tanggal_mulai);

        if($kontrak->jenis !== 'PKWTT'){
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
        $year = Carbon::parse($kontrak->issued_date)->format('Y');

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
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Database error: ' . $e->getMessage()], 500);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Model not found: ' . $e->getMessage()], 404);
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
                // $kontrak_exist = Kontrak::where('karyawan_id', $karyawan->id)->where('status', 'DONE')->orderBy('tanggal_mulai', 'DESC')->exists();
                $new_data = $karyawan->whereNotNull('tanggal_mulai')->whereNull('tanggal_selesai')->exists();

                if($new_data){
                    $existingCutiBersama = Event::whereDate('tanggal_mulai', '<=', $kontrak->tanggal_selesai)->where('jenis_event', 'CB');
                    if($existingCutiBersama->exists()){
                        foreach($existingCutiBersama->get() as $cutiBersama){
                            $jatah_cuti_bersama = $karyawan->sisa_cuti_bersama - $cutiBersama->durasi;
                            if($jatah_cuti_bersama >= 0){
                                $karyawan->sisa_cuti_bersama = $jatah_cuti_bersama;
                                $karyawan->save();
                            } else {
                                $karyawan->sisa_cuti_bersama = 0;
                                $karyawan->hutang_cuti = abs($jatah_cuti_bersama);
                                $karyawan->save();
                            }
                        }
                    }
                } else {
                    $existingCutiBersama = Event::whereDate('tanggal_mulai', '<=', $kontrak->tanggal_selesai)->where('jenis_event', 'CB');
                    if($existingCutiBersama->exists()){
                        //JIKA ADA
                        foreach($existingCutiBersama->get() as $cutiBersama){
                            //CEK APAKAH ADA KONTRAK YANG MEMILIKI TANGGAL SELESAI LEBIH DARI CUTI BERSAMA (KONTRAK SELESAI SETELAH CUTI BERSAMA)
                            $existingKontrak = Kontrak::where('karyawan_id', $karyawan->id_karyawan)->where('status', 'DONE')->where('tanggal_selesai', '>=', $cutiBersama->tanggal_mulai)->exists();
                            //JIKA SUDAH ADA, MAKA TIDAK PERLU DIKURANGI SISA CUTI BERSAMA (KARENA SUDAH DIKURANGIN SEBELUMNYA)
                            if(!$existingKontrak){
                                $jatah_cuti_bersama = $karyawan->sisa_cuti_bersama - $cutiBersama->durasi;
                                if($jatah_cuti_bersama >= 0){
                                    $karyawan->sisa_cuti_bersama = $jatah_cuti_bersama;
                                    $karyawan->save();
                                } else {
                                    $karyawan->sisa_cuti_bersama = 0;
                                    $karyawan->hutang_cuti = abs($jatah_cuti_bersama);
                                    $karyawan->save();
                                }
                            }
                        }
                    }
                }
                
                if($kontrak->jenis == 'PKWTT'){
                    $karyawan->tanggal_selesai = null;
                } else {
                    $karyawan->tanggal_selesai = $kontrak->tanggal_selesai;
                }
            }

            //update status kontrak
            $kontrak->status = 'DONE';
            $kontrak->save();

            $karyawan->jenis_kontrak = $kontrak->jenis;
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
        $file = $request->file('kontrak_file');
        $organisasi_id = auth()->user()->organisasi_id;
        if ($organisasi_id == '1'){
            $tempat_administrasi = 'Karawang';
        } else {
            $tempat_administrasi = 'Purwakarta';
        }
        
        $validator = Validator::make($request->all(), [
            'kontrak_file' => 'required|mimes:xlsx,xls'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'File Harus bertipe Excel!'], 400);
        }

        DB::beginTransaction();
        try {

            if($request->hasFile('kontrak_file')){
                $kontrak_records = 'KN_' . time() . '.' . $file->getClientOriginalExtension();
                $kontrak_file = $file->storeAs("attachment/upload-kontrak", $kontrak_records);
            } 

            if (file_exists(storage_path("app/public/".$kontrak_file))) {
                $spreadsheet = PhpSpreadsheetIOFactory::load(storage_path("app/public/".$kontrak_file));
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();
                $chunkSize = 100;
                $karyawans = [];
                //Chunck data agar tidak terlalu banyak
                for ($i = 1; $i <= count($data); $i += $chunkSize) {
                    $chunk = array_slice($data, $i - 1, $chunkSize);
                    foreach ($chunk as $index => $row) {
                        if ($index < 1) { 
                            continue;
                        }

                        $karyawans[] = Karyawan::where('ni_karyawan', $row[0])->first()->id_karyawan;

                        //Convert tanggal mulai dan selesai ke format Ymd jika ada
                        if($row[7] !== null){
                            try {
                                $tanggal_mulai = Carbon::createFromFormat('d/m/Y', $row[7])->format('Y-m-d');
                            } catch (Exception $e) {
                                return response()->json(['message' => 'Format tanggal mulai salah!'], 402);
                            }
                        } 

                        if($row[8] !== null){
                            try {
                                $tanggal_selesai = Carbon::createFromFormat('d/m/Y', $row[8])->format('Y-m-d');
                            } catch (Exception $e) {
                                return response()->json(['message' => 'Format tanggal selesai salah!'], 402);
                            }
                        }

                        //Validasi Kolom Numeric
                        if (!is_numeric($row[2]) || !is_numeric($row[5]) || $row[5] < 0 || !is_numeric($row[6]) || $row[6] < 0) {
                            return response()->json(['message' => 'Kolom ID Posisi, Durasi dan Salary harus berupa numeric!'], 402);
                        }

                        $posisi_id = Posisi::where('id_posisi', $row[2])->first()->id_posisi;
                        if($posisi_id == null){
                            return response()->json(['message' => 'Posisi dengan ID '.$row[2].' tidak ditemukan!'], 404);
                        }

                        //Validasi Jenis Kontrak
                        if (!in_array($row[4], ['PKWT', 'PKWTT', 'MAGANG'])) {
                            return response()->json(['message' => 'Jenis kontrak pada baris ' . ($index + 1) . ' harus PKWT, PKWTT, atau MAGANG!'], 402);
                        }

                        //Validasi Nomor Induk Karyawan
                        $karyawan = Karyawan::where('ni_karyawan', $row[0])->first();
                        if($karyawan->id_karyawan == null){
                            return response()->json(['message' => 'Karyawan dengan Nomor Induk '.$row[0].' tidak ditemukan!'], 404);
                        }

                        //Validasi Tempat Administrasi
                        if (!in_array($row[9], ['Karawang', 'Purwakarta'])) {
                            return response()->json(['message' => 'Tempat administasi tidak sesuai format!'], 402);
                        }

                        //Update data karyawan
                        $karyawan->jenis_kontrak = $row[4];
                        $karyawan->status_karyawan = 'AT';

                        if(($karyawan->tanggal_selesai < $tanggal_selesai || $karyawan->tanggal_selesai == null) && $row[4] !== 'PKWTT'){
                            $karyawan->tanggal_selesai = $tanggal_selesai;
                        }

                        //CEK APAKAH ADA CUTI BERSAMA SEBELUM TANGGAL SELESAI KONTRAK YANG BARU DI UPLOAD
                        $existingCutiBersama = Event::whereDate('tanggal_mulai', '<=', $tanggal_selesai)->where('jenis_event', 'CB');
                        if($existingCutiBersama->exists()){
                            //JIKA ADA
                            foreach($existingCutiBersama->get() as $cutiBersama){
                                //CEK APAKAH ADA KONTRAK YANG MEMILIKI TANGGAL SELESAI LEBIH DARI CUTI BERSAMA (KONTRAK SELESAI SETELAH CUTI BERSAMA)
                                $existingKontrak = Kontrak::where('karyawan_id', $karyawan->id_karyawan)->where('status', 'DONE')->where('tanggal_selesai', '>=', $cutiBersama->mulai)->exists();
                                //JIKA SUDAH ADA, MAKA TIDAK PERLU DIKURANGI SISA CUTI BERSAMA (KARENA SUDAH DIKURANGIN SEBELUMNYA)
                                if(!$existingKontrak){
                                    $jatah_cuti_bersama = $karyawan->sisa_cuti_bersama - $cutiBersama->durasi;
                                    if($jatah_cuti_bersama >= 0){
                                        $karyawan->sisa_cuti_bersama = $jatah_cuti_bersama;
                                        $karyawan->save();
                                    } else {
                                        $karyawan->sisa_cuti_bersama = 0;
                                        $karyawan->hutang_cuti = abs($jatah_cuti_bersama);
                                        $karyawan->save();
                                    }
                                }
                            }
                        }
                        
                        $karyawan->save();

                        // Input Kontrak
                        Kontrak::create([
                            'no_surat' => $row[3],
                            'id_kontrak' => 'KONTRAK-'. Str::random(4) . '-' . now()->timestamp,
                            'karyawan_id' =>  $karyawan->id_karyawan,
                            'posisi_id' => $posisi_id,
                            'nama_posisi' => Posisi::find($posisi_id)->nama ? Posisi::find($posisi_id)->nama : '',
                            'jenis' => $row[4],
                            'status' => 'DONE',
                            'durasi' => $row[5],
                            'salary' => $row[6],
                            'deskripsi' => 'History Kontrak Karyawan',
                            'tanggal_mulai' => $tanggal_mulai,
                            'tanggal_selesai' => $tanggal_selesai,
                            'tempat_administrasi' => $row[9],
                            'isReactive' => 'N',
                            'organisasi_id' => $organisasi_id,
                            'issued_date' => Carbon::now()->format('Y-m-d'),
                        ]);
                    }
                }

                //Update sisa cuti bersama karyawan
                $array_karyawan = array_unique($karyawans);
                foreach ($array_karyawan as $kry){
                    $k = Karyawan::find($kry);
                    $kontrak = Kontrak::where('karyawan_id', $kry)->where('status', 'DONE')->orderBy('tanggal_selesai', 'DESC')->first();
                    if($k && $kontrak){
                        $existingCutiBersama = Event::whereDate('tanggal_mulai', '<=', $kontrak->tanggal_selesai)->where('jenis_event', 'CB');
                        if($existingCutiBersama->exists()){
                            foreach($existingCutiBersama->get() as $cutiBersama){
                                $jatah_cuti_bersama = $k->sisa_cuti_bersama - $cutiBersama->durasi;
                                if($jatah_cuti_bersama >= 0){
                                    $k->sisa_cuti_bersama = $jatah_cuti_bersama;
                                    $k->save();
                                } else {
                                    $k->sisa_cuti_bersama = 0;
                                    $k->hutang_cuti = abs($jatah_cuti_bersama);
                                    $k->save();
                                }
                            }
                        }
                    }
                } 
            } else {
                DB::rollBack();
                return response()->json(['message' => 'Terjadi kesalahan, silahkan upload ulang file!'], 404);
            }
            DB::commit();
            return response()->json(['message' => 'File berhasil di upload'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error processing the file: ' . $e->getMessage()], 500);
        }
    }
}
