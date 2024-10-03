<?php

namespace App\Http\Controllers\MasterData;

use Carbon\Carbon;
use App\Models\Grup;
use App\Models\User;
use App\Models\Seksi;
use App\Models\Divisi;
use App\Models\Posisi;
use App\Models\Jabatan;
use App\Models\Kontrak;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Organisasi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departemen = Departemen::all();
        $dataPage = [
            'pageTitle' => "Master Data - Export",
            'page' => 'masterdata-export',
            'departemen' => $departemen
        ];
        return view('pages.master-data.export.index', $dataPage);
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
        //
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

    public function export_master_data(Request $request){

        $departemen_karyawan = $request->departemen_karyawan;

        //Karyawan
        if($request->karyawan_aktif == 'Y') {
            if($departemen_karyawan){
                $karyawan = Karyawan::whereHas('posisi', function ($query) use ($request) {
                                $query->where('departemen_id', $request->departemen_karyawan);
                            })->where('status_karyawan', 'AKTIF')->get();
            } else {
                $karyawan = Karyawan::where('status_karyawan', 'AKTIF')->get();
            }
        } 
        
        if($request->karyawan_nonaktif == 'Y'){
            if($departemen_karyawan){
                $karyawan = Karyawan::whereHas('posisi', function ($query) use ($request) {
                    $query->where('departemen_id', $request->departemen_karyawan);
                })->where(function ($query) {
                    $query->whereIn('status_karyawan', ['RESIGN', 'TERMINASI', 'PENSIUN'])
                          ->orWhereNull('status_karyawan');
                })->get();
            } else {
                $karyawan = Karyawan::whereIn('status_karyawan',['RESIGN','TERMINASI','PENSIUN'])->orWhereNull('status_karyawan')->get();
            }
        } 
        
        if ($request->karyawan_aktif == 'Y' && $request->karyawan_nonaktif == 'Y'){
            if($departemen_karyawan){
                $karyawan = Karyawan::whereHas('posisi', function ($query) use ($request) {
                    $query->where('departemen_id', $request->departemen_karyawan);
                })->get();
            } else {
                $karyawan = Karyawan::all();
            }
        } else {
            $karyawan = [];
        }

        //Posisi
        $request->posisi == 'Y' ? $posisi = Posisi::all() : $posisi = [];

        //Divisi
        $request->divisi == 'Y' ? $divisi = Divisi::all() : $divisi = [];

        //Departemen
        $request->departemen == 'Y' ? $departemen = Departemen::all() : $departemen = [];

        //Seksi
        $request->seksi == 'Y' ? $seksi = Seksi::all() : $seksi = [];

        //Grup
        $request->grup == 'Y' ? $grup = Grup::all() : $grup = [];

        //Jabatan
        $request->jabatan == 'Y' ? $jabatan = Jabatan::all() : $jabatan = [];
        
        //Organisasi
        $request->organisasi == 'Y' ? $organisasi = Organisasi::all() : $organisasi = [];

        if(empty($karyawan) && empty($posisi) && empty($divisi) && empty($departemen) && empty($seksi) && empty($grup) && empty($jabatan) && empty($organisasi)){
            $spreadsheet = new Spreadsheet();
            $writer = new Xlsx($spreadsheet);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename=data-not-found.xlsx');
            header('Cache-Control: max-age=0');

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            exit();
        }

        $spreadsheet = new Spreadsheet();

        $fillStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF000000'
                ]
            ],
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 12,
            ],
        ];

        //NEW TEMPLATE KARYAWAN
        if(!empty($karyawan)){
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Karyawan');

            $row = 1;
            $col = 'A';

            $headers = [
                'NO',
                'NIK',
                'POSISI',
                'DEPARTEMEN',
                'JABATAN',
                'NAMA',
                'KONTRAK',
                'JENIS KELAMIN',
                'ALAMAT KTP',
                'DOMISILI',
                'TEMPAT LAHIR',
                'TANGGAL LAHIR',
                'STATUS KELUARGA',
                'KATEGORI KELUARGA',
                'AGAMA',
                'NO KK',
                'NIK KTP',
                'NPWP',
                'NO BPJS KETENAGAKERJAAN',
                'NO BPJS KESEHATAN',
                'NO HP',
                'TANGGAL BERGABUNG',
                'PENDIDIKAN TERAKHIR (TINGKAT)',
                'JURUSAN',
                'NAMA IBU KANDUNG',
                'NAMA BANK',
                'NO REKENING',
                'ATAS NAMA REKENING',
                'NO TELP DARURAT',
                'GOLONGAN DARAH',
                'EMAIL',
                'EMAIL CORPORATE',
                'JATAH CUTI',
                'HUTANG CUTI'
            ];

            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
                $col++;
            }

            $row = 2;

            $columns = [];
            for ($i = 'A'; $i !== 'AI'; $i++) {
                $columns[] = $i;
            }
            foreach ($columns as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            $sheet->setAutoFilter('A1:AH1');

            $i = 1;
            foreach ($karyawan as $data) {
                $email_corporate = $data->user->email;
                $dataPosisis = $data->posisi();
                $jabatan_merge = $dataPosisis->first()->jabatan->nama;
                $posisis = $dataPosisis->pluck('posisis.nama')->toArray();
                if(!empty($posisis)){
                    $formattedPosisi = array_map(function($posisi) {
                        return $posisi;
                    }, $posisis);
                    $posisi_merge = implode(' , ', $formattedPosisi);
                    $departemens = [];
                    foreach ($dataPosisis->get() as $item){
                        $departemens = [$item->departemen->nama];
                    }
                    $formattedDepartemen = array_map(function($departemen) {
                        return $departemen;
                    }, $departemens);
                    $departemen_merge = implode(' , ', $formattedDepartemen);
                } else {
                    $posisi_merge = '-';
                    $departemen_merge = '-';
                    $jabatan_merge = '-';
                }
        
                $sheet->setCellValue('A' . $row, $i);
                $sheet->setCellValue('B' . $row, $data->ni_karyawan);
                $sheet->setCellValue('C' . $row, $posisi_merge);
                $sheet->setCellValue('D' . $row, $departemen_merge);
                $sheet->setCellValue('E' . $row, $jabatan_merge);
                $sheet->setCellValue('F' . $row, $data->nama);
                $sheet->setCellValue('G' . $row, $data->jenis_kontrak);
                $sheet->setCellValue('H' . $row, $data->jenis_kelamin);
                $sheet->setCellValue('I' . $row, $data->alamat);
                $sheet->setCellValue('J' . $row, $data->domisili);
                $sheet->setCellValue('K' . $row, $data->tempat_lahir);
                $sheet->setCellValue('L' . $row, $data->tanggal_lahir);
                $sheet->setCellValue('M' . $row, $data->status_keluarga);
                $sheet->setCellValue('N' . $row, $data->kategori_keluarga);
                $sheet->setCellValue('O' . $row, $data->agama);
                $sheet->setCellValue('P' . $row, $data->no_kk);
                $sheet->setCellValue('Q' . $row, $data->nik);
                $sheet->setCellValue('R' . $row, $data->npwp);
                $sheet->setCellValue('S' . $row, $data->no_bpjs_kt);
                $sheet->setCellValue('T' . $row, $data->no_bpjs_ks);
                $sheet->setCellValue('U' . $row, $data->no_telp);
                $sheet->setCellValue('V' . $row, $data->tanggal_mulai);
                $sheet->setCellValue('W' . $row, $data->jenjang_pendidikan);
                $sheet->setCellValue('X' . $row, $data->jurusan_pendidikan);
                $sheet->setCellValue('Y' . $row, $data->nama_ibu_kandung);
                $sheet->setCellValue('Z' . $row, $data->nama_bank);
                $sheet->setCellValue('AA' . $row, $data->no_rekening);
                $sheet->setCellValue('AB' . $row, $data->nama_rekening);
                $sheet->setCellValue('AC' . $row, $data->no_telp_darurat);
                $sheet->setCellValue('AD' . $row, $data->gol_darah);
                $sheet->setCellValue('AE' . $row, $data->email);
                $sheet->setCellValue('AF' . $row, $email_corporate);
                $sheet->setCellValue('AG' . $row, $data->sisa_cuti_pribadi + $data->sisa_cuti_bersama);
                $sheet->setCellValue('AH' . $row, $data->hutang_cuti);
                $row++;
                $i++;
            }
        }

        if(!empty($posisi)){
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Posisi');

            $row = 1;
            $col = 'A';

            $headers = [
                'ID Posisi',
                'Posisi',
                'Atasan',
                'Jabatan',
                'Organisasi',
                'Divisi',
                'Departemen',
                'Seksi',
                'Grup'
            ];

            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
                $col++;
            }

            $row = 2;

            $columns = range('A', 'I');
            foreach ($columns as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            $sheet->setAutoFilter('A1:I1');

            foreach ($posisi as $data) {
                $sheet->setCellValue('A' . $row, $data->id_posisi);
                $sheet->setCellValue('B' . $row, $data->nama);
                $sheet->setCellValue('C' . $row, $data->parent?->nama);
                $sheet->setCellValue('D' . $row, $data->jabatan?->nama);
                $sheet->setCellValue('E' . $row, $data->organisasi?->nama);
                $sheet->setCellValue('F' . $row, $data->divisi?->nama);
                $sheet->setCellValue('G' . $row, $data->departemen?->nama);
                $sheet->setCellValue('H' . $row, $data->seksi?->nama);
                $sheet->setCellValue('I' . $row, $data->grup?->nama);
                $row++;
            }
        }

        if(!empty($organisasi)){
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Organisasi');

            $row = 1;
            $col = 'A';

            $headers = [
                'ID Organisasi',
                'Nama Organisasi',
                'Alias',
                'Alamat',
            ];

            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
                $col++;
            }

            $row = 2;

            $columns = range('A', 'D');
            foreach ($columns as $column) {
                $sheet->getColumnDimension($column)->setWidth(30);
            }
            $sheet->setAutoFilter('A1:D1');

            foreach ($organisasi as $data) {
                $sheet->setCellValue('A' . $row, $data->id_organisasi);
                $sheet->setCellValue('B' . $row, $data->nama);
                $sheet->setCellValue('C' . $row, ($data->nama == 'KIM' ? 'TCF3' : ($data->nama == 'SADANG' ? 'TCF2' : 'TCF1')));
                $sheet->setCellValue('D' . $row, $data->alamat);
                $row++;
            }
        }

        if(!empty($divisi)){
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Divisi');

            $row = 1;
            $col = 'A';

            $headers = [
                'ID Divisi',
                'Nama Divisi',
            ];

            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
                $col++;
            }

            $row = 2;

            $columns = range('A', 'I');
            foreach ($columns as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            $sheet->setAutoFilter('A1:B1');

            foreach ($divisi as $data) {
                $sheet->setCellValue('A' . $row, $data->id_divisi);
                $sheet->setCellValue('B' . $row, $data->nama);
                $row++;
            }
        }

        if(!empty($departemen)){
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Departemen');

            $row = 1;
            $col = 'A';

            $headers = [
                'ID Departemen',
                'Nama Departemen',
                'Nama Divisi',
            ];

            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
                $col++;
            }

            $row = 2;

            $columns = range('A', 'C');
            foreach ($columns as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            $sheet->setAutoFilter('A1:C1');

            foreach ($departemen as $data) {
                $sheet->setCellValue('A' . $row, $data->id_departemen);
                $sheet->setCellValue('B' . $row, $data->nama);
                $sheet->setCellValue('C' . $row, $data->divisi?->nama);
                $row++;
            }
        }

        if(!empty($seksi)){
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Seksi');

            $row = 1;
            $col = 'A';

            $headers = [
                'ID Seksi',
                'Nama Seksi',
                'Nama Departemen',
                'Nama Divisi',
            ];

            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
                $col++;
            }

            $row = 2;

            $columns = range('A', 'D');
            foreach ($columns as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            $sheet->setAutoFilter('A1:D1');

            foreach ($seksi as $data) {
                $sheet->setCellValue('A' . $row, $data->id_seksi);
                $sheet->setCellValue('B' . $row, $data->nama);
                $sheet->setCellValue('C' . $row, $data->departemen?->nama);
                $sheet->setCellValue('D' . $row, $data->divisi?->nama);
                $row++;
            }
        }

        if(!empty($grup)){
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Grup');

            $row = 1;
            $col = 'A';

            $headers = [
                'ID Grup',
                'Nama Grup',
            ];

            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
                $col++;
            }

            $row = 2;

            $columns = range('A', 'B');
            foreach ($columns as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            $sheet->setAutoFilter('A1:B1');

            foreach ($grup as $data) {
                $sheet->setCellValue('A' . $row, $data->id_grup);
                $sheet->setCellValue('B' . $row, $data->nama);
                $row++;
            }
        }

        if(!empty($jabatan)){
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Jabatan');

            $row = 1;
            $col = 'A';

            $headers = [
                'ID Jabatan',
                'Nama Jabatan',
            ];

            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
                $col++;
            }

            $row = 2;

            $columns = range('A', 'B');
            foreach ($columns as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            $sheet->setAutoFilter('A1:B1');

            foreach ($jabatan as $data) {
                $sheet->setCellValue('A' . $row, $data->id_jabatan);
                $sheet->setCellValue('B' . $row, $data->nama);
                $row++;
            }
        }

        $spreadsheet->removeSheetByIndex(0);
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=master-data-export.xlsx');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit();
    }

    public function export_kontrak(Request $request){

        $kontrak_from = $request->kontrak_from;
        $kontrak_to = $request->kontrak_to;
        $durasi = $request->durasi;
        $departemen = $request->departemen_kontrak;
        $status = $request->status_kontrak;
        $jenis_kontrak = $request->jenis_kontrak;

        $kontrak = Kontrak::select(
            'kontraks.id_kontrak',
            'kontraks.karyawan_id',
            'kontraks.posisi_id',
            'kontraks.nama_posisi',
            'kontraks.no_surat',
            'kontraks.tempat_administrasi',
            'kontraks.issued_date',
            'kontraks.durasi',
            'kontraks.salary',
            'kontraks.deskripsi',
            'kontraks.tanggal_mulai as tanggal_mulai_kontrak',
            'kontraks.tanggal_selesai as tanggal_selesai_kontrak',
            'kontraks.jenis',
            'kontraks.status',
            'kontraks.attachment',
            'kontraks.evidence',
            'kontraks.isReactive',
            'karyawans.nama as nama_karyawan',
            'karyawans.ni_karyawan as nik_karyawan',
            'departemens.nama as nama_departemen'
        )
        ->leftJoin('karyawans', 'kontraks.karyawan_id', 'karyawans.id_karyawan')
        ->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
        ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
        ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')
        ->groupBy(
            'kontraks.id_kontrak',
            'kontraks.karyawan_id',
            'kontraks.posisi_id',
            'kontraks.nama_posisi',
            'kontraks.no_surat',
            'kontraks.tempat_administrasi',
            'kontraks.issued_date',
            'kontraks.durasi',
            'kontraks.salary',
            'kontraks.deskripsi',
            'kontraks.tanggal_mulai',
            'kontraks.tanggal_selesai',
            'kontraks.jenis',
            'kontraks.status',
            'kontraks.attachment',
            'kontraks.evidence',
            'kontraks.isReactive',
            'karyawans.nama',
            'karyawans.ni_karyawan',
            'departemens.nama'
        );

        //Menghitung jumlah hari kerja
        $kontrak->selectRaw('
            (kontraks.tanggal_selesai::date - kontraks.tanggal_mulai::date + 1) -
            (SELECT COUNT(*) FROM generate_series(kontraks.tanggal_mulai, kontraks.tanggal_selesai, interval \'1 day\') AS series
            WHERE EXTRACT(DOW FROM series) IN (0, 6)) as jumlah_hari
        ');

        if($departemen) {
            $kontrak->where('departemens.id_departemen', $departemen);
        }

        if($status) {
            $kontrak->where('kontraks.status', $status);
        }

        if($jenis_kontrak) {
            $kontrak->where('kontraks.jenis', $jenis_kontrak);
        }

        if ($kontrak_from) {
            $kontrak->whereYear('kontraks.tanggal_mulai', '>=', Carbon::parse($kontrak_from)->year)
                ->whereMonth('kontraks.tanggal_mulai', '>=', Carbon::parse($kontrak_from)->month);
        }
        if ($kontrak_to) {
            $kontrak->whereYear('kontraks.tanggal_selesai', '<=', Carbon::parse($kontrak_to)->year)
                ->whereMonth('kontraks.tanggal_selesai', '<=', Carbon::parse($kontrak_to)->month);
        }
        if($durasi) {
            $kontrak->where('durasi', $durasi);
        };

        $kontrak = $kontrak->get();
        $spreadsheet = new Spreadsheet();

        $fillStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF000000'
                ]
            ],
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 12,
            ],
        ];

        if($kontrak){
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Kontrak');

            $row = 1;
            $col = 'A';

            $headers = [
                'ID KONTRAK',
                'NIK',
                'DEPARTEMEN',
                'POSISI',
                'NAMA',
                'NO PERJANJIAN',
                'HARI',
                'AWAL',
                'AKHIR',
                'JENIS',
                'TEMPAT ADMINISTRASI',
                'STATUS',
                'DURASI',
                'SALARY',
                'DESKRIPSI',
                'DOCUMENT CREATED',
            ];

            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
                $col++;
            }

            $row = 2;

            $columns = range('A', 'P');
            foreach ($columns as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            $sheet->setAutoFilter('A1:P1');

            foreach ($kontrak as $data) {

                $carbonDate  = Carbon::parse($data->issued_date);
                $year = $carbonDate->year;

                $sheet->setCellValue('A' . $row, $data->id_kontrak);
                $sheet->setCellValue('B' . $row, $data->nik_karyawan);
                $sheet->setCellValue('C' . $row, $data->nama_departemen);
                $sheet->setCellValue('D' . $row, $data->nama_posisi);
                $sheet->setCellValue('E' . $row, $data->nama_karyawan);
                $sheet->setCellValue('F' . $row, $data->no_surat);
                $sheet->setCellValue('G' . $row, $data->jumlah_hari);
                $sheet->setCellValue('H' . $row, $data->tanggal_mulai_kontrak);
                $sheet->setCellValue('I' . $row, $data->tanggal_selesai_kontrak);
                $sheet->setCellValue('J' . $row, $data->jenis);
                $sheet->setCellValue('K' . $row, $data->tempat_administrasi);
                $sheet->setCellValue('L' . $row, $data->status);
                $sheet->setCellValue('M' . $row, $data->durasi);
                $sheet->setCellValue('N' . $row, $data->salary);
                $sheet->setCellValue('O' . $row, $data->deskripsi);
                $sheet->setCellValue('P' . $row, $data->issued_date);
                $row++;
            }
        }

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=kontrak-export.xlsx');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit();
    }
}
