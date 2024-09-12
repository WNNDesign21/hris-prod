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
        $dataPage = [
            'pageTitle' => "Master Data - Export",
            'page' => 'masterdata-export',
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

        //Karyawan
        if($request->karyawan_aktif == 'Y') {
            $karyawan = Karyawan::where('status_karyawan', 'AKTIF')->get();
        } elseif($request->karyawan_nonaktif == 'Y'){
            $karyawan = Karyawan::whereIn('status_karyawan',['RESIGN','TERMINASI','PENSIUN'])->get();
        } elseif ($request->karyawan_aktif == 'Y' && $request->karyawan_nonaktif == 'Y'){
            $karyawan = Karyawan::all();
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
                    'argb' => 'FFFFFF00'
                ]
            ],
            'font' => [
                'bold' => true,
            ],
        ];

        if(!empty($karyawan)){
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Karyawan');

            $row = 1;
            $col = 'A';

            $headers = [
                'ID Karyawan',
                'Posisi',
                'Username',
                'Nama',
                'No KTP',
                'NIK',
                'Tempat Lahir',
                'Tanggal Lahir',
                'Alamat',
                'Email',
                'No Telp',
                'Golongan Darah',
                'Jenis Kelamin',
                'Status Keluarga',
                'NPWP',
                'No BPJS Kesehatan',
                'No BPJS Ketenagakerjaan',
                'Jenis Kontrak',
                'Status Karyawan',
                'Sisa Cuti',
                'Tahun Masuk',
                'Tahun Keluar',
            ];

            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
                $col++;
            }

            $row = 2;

            $columns = range('A', 'V');
            foreach ($columns as $column) {
                $sheet->getColumnDimension($column)->setWidth(35);
            }
            $sheet->setAutoFilter('A1:V1');

            foreach ($karyawan as $data) {
                $posisis = $data->posisi()->pluck('posisis.nama')->toArray();
                $formattedPosisi = array_map(function($posisi) {
                    return $posisi;
                }, $posisis);
                $posisi_merge = implode(' , ', $formattedPosisi);
        
                $sheet->setCellValue('A' . $row, $data->id_karyawan);
                $sheet->setCellValue('B' . $row, $posisi_merge);
                $sheet->setCellValue('C' . $row, $data->user->username);
                $sheet->setCellValue('D' . $row, $data->nama);
                $sheet->setCellValue('E' . $row, $data->no_ktp);
                $sheet->setCellValue('F' . $row, $data->nik);
                $sheet->setCellValue('G' . $row, $data->tempat_lahir);
                $sheet->setCellValue('H' . $row, $data->tanggal_lahir);
                $sheet->setCellValue('I' . $row, $data->alamat);
                $sheet->setCellValue('J' . $row, $data->email);
                $sheet->setCellValue('K' . $row, $data->no_telp);
                $sheet->setCellValue('L' . $row, $data->gol_darah);
                $sheet->setCellValue('M' . $row, $data->jenis_kelamin);
                $sheet->setCellValue('N' . $row, $data->status_keluarga);
                $sheet->setCellValue('O' . $row, $data->npwp);
                $sheet->setCellValue('P' . $row, $data->no_bpjs_ks);
                $sheet->setCellValue('Q' . $row, $data->no_bpjs_kt);
                $sheet->setCellValue('R' . $row, $data->jenis_kontrak);
                $sheet->setCellValue('S' . $row, $data->status_karyawan);
                $sheet->setCellValue('T' . $row, $data->sisa_cuti);
                $sheet->setCellValue('U' . $row, $data->tahun_masuk);
                $sheet->setCellValue('V' . $row, $data->tahun_keluar);
                $row++;
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
                $sheet->getColumnDimension($column)->setWidth(35);
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
                $sheet->getColumnDimension($column)->setWidth(35);
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
                $sheet->getColumnDimension($column)->setWidth(35);
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
                $sheet->getColumnDimension($column)->setWidth(35);
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
                $sheet->getColumnDimension($column)->setWidth(35);
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
                $sheet->getColumnDimension($column)->setWidth(35);
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

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit();
    }

    public function export_kontrak(Request $request){

        $kontrak_from = $request->kontrak_from;
        $kontrak_to = $request->kontrak_to;
        $durasi = $request->durasi;

        $kontrak = Kontrak::query();

        if ($kontrak_from) {
            $kontrak->where('tanggal_mulai', 'ILIKE', '%' . $kontrak_from . '%');
        }

        if ($kontrak_to) {
            $kontrak->where('tanggal_selesai', 'ILIKE', '%' . $kontrak_to . '%');
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
                    'argb' => 'FFFFFF00'
                ]
            ],
            'font' => [
                'bold' => true,
            ],
        ];

        if($kontrak){
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Kontrak');

            $row = 1;
            $col = 'A';

            $headers = [
                'ID Kontrak',
                'ID Karyawan',
                'Nama Karyawan',
                'Posisi',
                'No Surat',
                'Jenis',
                'Status',
                'Durasi',
                'Salary',
                'Deskripsi',
                'Tanggal Mulai',
                'Tanggal Selesai',
                'Issued Date',
                'Tempat Administrasi',
                // 'Approved By',
                // 'Approved Date'
            ];

            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
                $col++;
            }

            $row = 2;

            $columns = range('A', 'N');
            foreach ($columns as $column) {
                $sheet->getColumnDimension($column)->setWidth(35);
            }
            $sheet->setAutoFilter('A1:N1');

            foreach ($kontrak as $data) {

                $carbonDate  = Carbon::parse($data->issued_date);
                $year = $carbonDate->year;

                $sheet->setCellValue('A' . $row, $data->id_kontrak);
                $sheet->setCellValue('B' . $row, $data->karyawan_id);
                $sheet->setCellValue('C' . $row, $data->karyawan->nama);
                $sheet->setCellValue('D' . $row, $data->nama_posisi);
                $sheet->setCellValue('E' . $row, 'No.'.$data->no_surat.'/'.$data->jenis.'-I'.'/HRD-'.($data->tempat_administrasi == 'Karawang' ? 'TCF3' : 'TCF2')."/V"."/".$year);
                $sheet->setCellValue('F' . $row, $data->jenis);
                $sheet->setCellValue('G' . $row, $data->status);
                $sheet->setCellValue('H' . $row, $data->durasi);
                $sheet->setCellValue('I' . $row, $data->salary);
                $sheet->setCellValue('J' . $row, $data->deskripsi);
                $sheet->setCellValue('K' . $row, $data->tanggal_mulai);
                $sheet->setCellValue('L' . $row, $data->tanggal_selesai);
                $sheet->setCellValue('M' . $row, $data->issued_date);
                $sheet->setCellValue('N' . $row, $data->tempat_administrasi);
                // $sheet->setCellValue('O' . $row, Karyawan::find($data->status_change_by)?->nama);
                // $sheet->setCellValue('P' . $row, $data->status_change_date);
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
