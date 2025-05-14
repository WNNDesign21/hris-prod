<?php

namespace App\Http\Controllers\Cutie;

use Carbon\Carbon;
use App\Models\Departemen;
use Illuminate\Http\Request;
use App\Services\CutiService;
use App\Services\KaryawanService;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExportController extends Controller
{
    private $cutiService;
    public function __construct(CutiService $cutiService)
    {
        $this->cutiService = $cutiService;
    }

    public function index()
    {
        $departemens = Departemen::all();
        $dataPage = [
            'pageTitle' => "Cuti-E - Export Data",
            'page' => 'cutie-export',
            'departemen' => $departemens,
        ];
        return view('pages.cuti-e.export-cuti', $dataPage);
    }

    public function export(Request $request)
    {
        $departemen_id = $request->departemen_id;
        $organisasi_id = auth()->user()->organisasi_id;
        $pribadi = $request->pribadi;
        $khusus = $request->khusus;
        $tahun = $request->tahun;
        $bulan = $request->bulan;

        //CREATE EXCEL FILE
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

        if($bulan){
            $jenis_cuti = [];
            $dataFilter = [];

            if($pribadi == 'Y'){
            array_push($jenis_cuti, 'PRIBADI');
            }

            if($khusus == 'Y'){
                array_push($jenis_cuti, 'KHUSUS');
            }

            if(!empty($jenis_cuti)){
                $dataFilter['jenis_cuti'] = $jenis_cuti;
            }

            if ($departemen_id !== 'all'){
                $dataFilter['departemen_id'] = $departemen_id;
            }

            $dataFilter['organisasi_id'] = $organisasi_id;
            $dataFilter['year'] = $tahun;
            $dataFilter['monthly'] = $bulan;
            $monthlyCutie = $this->cutiService->getWithFilters($dataFilter, ['*'])->get();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle(Carbon::createFromFormat('m', $bulan)->format('F Y'));
            $row = 1;
            $col = 'A';
            $headers = [
                'No',
                'Nomor Induk Karyawan',
                'Nama',
                'Departemen',
                'Jabatan',
                'Cuti Khusus',
                'Cuti Pribadi',
                'Cuti 1',
                'Cuti 2',
                'Cuti 3',
                'Cuti 4',
                'Cuti 5',
                'Cuti 6',
                'Sisa Cuti Pribadi'
            ];

            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
                $col++;
            }

            $row = 2;

            $columns = range('A', 'N');
            foreach ($columns as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            $sheet->setAutoFilter('A1:N1');

            $jumlah_cuti_khusus = 0;
            $jumlah_cuti_pribadi = 0;
            $tanggal_cuti = [];
            foreach ($monthlyCutie as $index => $c) {

                if($c->jenis_cuti == 'KHUSUS'){
                    //MENDAPATKAN DURASI CUTI KHUSUS
                    $jumlah_cuti_khusus += $c->durasi_cuti;
                } else {
                    //MENDAPATKAN DURASI CUTI PRIBADI
                    $jumlah_cuti_pribadi += $c->durasi_cuti;
                    if($c->durasi_cuti > 1){
                        $range_date_cuti =  Carbon::parse($c->rencana_mulai_cuti)->toPeriod(Carbon::parse($c->rencana_selesai_cuti))->toArray();
                        foreach ($range_date_cuti as $r) {
                            $tanggal_cuti[] = $r->format('Y-m-d');
                        }
                    } else {
                        $tanggal_cuti[] = $c->rencana_mulai_cuti;
                    }
                }

                if(isset($monthlyCutie[$index+1])){
                    if($monthlyCutie[$index+1]->karyawan_id !== $c->karyawan_id){
                        $sheet->setCellValue('A' . $row, $row - 1);
                        $sheet->setCellValueExplicit('B' . $row, str_replace(',', '.', $c->karyawan->ni_karyawan), DataType::TYPE_STRING);
                        $sheet->setCellValue('C' . $row, $c->karyawan->nama);
                        $sheet->setCellValue('D' . $row, $c->karyawan->posisi[0]?->departemen?->nama);
                        $sheet->setCellValue('E' . $row, $c->karyawan->posisi[0]?->nama);
                        $sheet->setCellValue('F' . $row, $jumlah_cuti_khusus.' Hari');
                        $sheet->setCellValue('G' . $row, $jumlah_cuti_pribadi.' Hari');
                        $sheet->setCellValue('H' . $row, isset($tanggal_cuti[0]) ? $tanggal_cuti[0] : '');
                        $sheet->setCellValue('I' . $row, isset($tanggal_cuti[1]) ? $tanggal_cuti[1] : '');
                        $sheet->setCellValue('J' . $row, isset($tanggal_cuti[2]) ? $tanggal_cuti[2] : '');
                        $sheet->setCellValue('K' . $row, isset($tanggal_cuti[3]) ? $tanggal_cuti[3] : '');
                        $sheet->setCellValue('L' . $row, isset($tanggal_cuti[4]) ? $tanggal_cuti[4] : '');
                        $sheet->setCellValue('M' . $row, isset($tanggal_cuti[5]) ? $tanggal_cuti[5] : '');
                        $sheet->setCellValue('N' . $row, $c->karyawan->sisa_cuti_pribadi.' Hari');

                        $row++;

                        $jumlah_cuti_khusus = 0;
                        $jumlah_cuti_pribadi = 0;
                        $tanggal_cuti = [];
                    } else {
                        continue;
                    }
                } else {
                    $sheet->setCellValue('A' . $row, $row - 1);
                    $sheet->setCellValueExplicit('B' . $row, str_replace(',', '.', $c->karyawan->ni_karyawan), DataType::TYPE_STRING);
                    $sheet->setCellValue('C' . $row, $c->karyawan->nama);
                    $sheet->setCellValue('D' . $row, $c->karyawan->posisi[0]?->departemen?->nama);
                    $sheet->setCellValue('E' . $row, $c->karyawan->posisi[0]?->nama);
                    $sheet->setCellValue('F' . $row, $jumlah_cuti_khusus.' Hari');
                    $sheet->setCellValue('G' . $row, $jumlah_cuti_pribadi.' Hari');
                    $sheet->setCellValue('H' . $row, isset($tanggal_cuti[0]) ? $tanggal_cuti[0] : '');
                    $sheet->setCellValue('I' . $row, isset($tanggal_cuti[1]) ? $tanggal_cuti[1] : '');
                    $sheet->setCellValue('J' . $row, isset($tanggal_cuti[2]) ? $tanggal_cuti[2] : '');
                    $sheet->setCellValue('K' . $row, isset($tanggal_cuti[3]) ? $tanggal_cuti[3] : '');
                    $sheet->setCellValue('L' . $row, isset($tanggal_cuti[4]) ? $tanggal_cuti[4] : '');
                    $sheet->setCellValue('M' . $row, isset($tanggal_cuti[5]) ? $tanggal_cuti[5] : '');
                    $sheet->setCellValue('N' . $row, $c->karyawan->sisa_cuti_pribadi.' Hari');
                }
            }
        } else {
            for($i = 1; $i <= 12; $i++){
                $i = str_pad($i, 2, '0', STR_PAD_LEFT);
                $jenis_cuti = [];
                $dataFilter = [];

                if($pribadi == 'Y'){
                    array_push($jenis_cuti, 'PRIBADI');
                }

                if($khusus == 'Y'){
                    array_push($jenis_cuti, 'KHUSUS');
                }

                if(!empty($jenis_cuti)){
                    $dataFilter['jenis_cuti'] = $jenis_cuti;
                }

                if ($departemen_id !== 'all'){
                    $dataFilter['departemen_id'] = $departemen_id;
                }

                $dataFilter['organisasi_id'] = $organisasi_id;
                $dataFilter['year'] = $tahun;
                $dataFilter['monthly'] = Carbon::createFromFormat('m', $i)->month;
                $monthlyCuties = $this->cutiService->getWithFilters($dataFilter, ['*'])->get();

                //Kalo bulan itu kosong jangan di export
                if($monthlyCuties->isEmpty()){
                    continue;
                }

                $sheet = $spreadsheet->createSheet($i - 1);
                $sheet->setTitle(Carbon::createFromFormat('m', $i)->format('F Y'));
                $row = 1;
                $col = 'A';
                $headers = [
                    'No',
                    'Nomor Induk Karyawan',
                    'Nama',
                    'Departemen',
                    'Jabatan',
                    'Cuti Khusus',
                    'Cuti Pribadi',
                    'Cuti 1',
                    'Cuti 2',
                    'Cuti 3',
                    'Cuti 4',
                    'Cuti 5',
                    'Cuti 6',
                    'Sisa Cuti Pribadi'
                ];

                foreach ($headers as $header) {
                    $sheet->setCellValue($col . '1', $header);
                    $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
                    $col++;
                }

                $row = 2;

                $columns = range('A', 'N');
                foreach ($columns as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
                $sheet->setAutoFilter('A1:N1');

                $jumlah_cuti_khusus = 0;
                $jumlah_cuti_pribadi = 0;
                $tanggal_cuti = [];
                foreach ($monthlyCuties as $index => $c) {

                    if($c->jenis_cuti == 'KHUSUS'){
                        //MENDAPATKAN DURASI CUTI KHUSUS
                        $jumlah_cuti_khusus += $c->durasi_cuti;
                    } else {
                        //MENDAPATKAN DURASI CUTI PRIBADI
                        $jumlah_cuti_pribadi += $c->durasi_cuti;
                        if($c->durasi_cuti > 1){
                            $range_date_cuti =  Carbon::parse($c->rencana_mulai_cuti)->toPeriod(Carbon::parse($c->rencana_selesai_cuti))->toArray();
                            foreach ($range_date_cuti as $r) {
                                $tanggal_cuti[] = $r->format('Y-m-d');
                            }
                        } else {
                            $tanggal_cuti[] = $c->rencana_mulai_cuti;
                        }
                    }

                    if(isset($monthlyCuties[$index+1])){
                        if($monthlyCuties[$index+1]->karyawan_id !== $c->karyawan_id){
                            $sheet->setCellValue('A' . $row, $row - 1);
                            $sheet->setCellValueExplicit('B' . $row, str_replace(',', '.', $c->karyawan->ni_karyawan), DataType::TYPE_STRING);
                            $sheet->setCellValue('C' . $row, $c->karyawan->nama);
                            $sheet->setCellValue('D' . $row, $c->karyawan->posisi[0]?->departemen?->nama);
                            $sheet->setCellValue('E' . $row, $c->karyawan->posisi[0]?->nama);
                            $sheet->setCellValue('F' . $row, $jumlah_cuti_khusus.' Hari');
                            $sheet->setCellValue('G' . $row, $jumlah_cuti_pribadi.' Hari');
                            $sheet->setCellValue('H' . $row, isset($tanggal_cuti[0]) ? $tanggal_cuti[0] : '');
                            $sheet->setCellValue('I' . $row, isset($tanggal_cuti[1]) ? $tanggal_cuti[1] : '');
                            $sheet->setCellValue('J' . $row, isset($tanggal_cuti[2]) ? $tanggal_cuti[2] : '');
                            $sheet->setCellValue('K' . $row, isset($tanggal_cuti[3]) ? $tanggal_cuti[3] : '');
                            $sheet->setCellValue('L' . $row, isset($tanggal_cuti[4]) ? $tanggal_cuti[4] : '');
                            $sheet->setCellValue('M' . $row, isset($tanggal_cuti[5]) ? $tanggal_cuti[5] : '');
                            $sheet->setCellValue('N' . $row, $c->karyawan->sisa_cuti_pribadi.' Hari');

                            $row++;

                            $jumlah_cuti_khusus = 0;
                            $jumlah_cuti_pribadi = 0;
                            $tanggal_cuti = [];
                        } else {
                            continue;
                        }
                    } else {
                        $sheet->setCellValue('A' . $row, $row - 1);
                        $sheet->setCellValueExplicit('B' . $row, $c->karyawan->ni_karyawan, DataType::TYPE_STRING);
                        $sheet->setCellValue('C' . $row, $c->karyawan->nama);
                        $sheet->setCellValue('D' . $row, $c->karyawan->posisi[0]?->departemen?->nama);
                        $sheet->setCellValue('E' . $row, $c->karyawan->posisi[0]?->nama);
                        $sheet->setCellValue('F' . $row, $jumlah_cuti_khusus.' Hari');
                        $sheet->setCellValue('G' . $row, $jumlah_cuti_pribadi.' Hari');
                        $sheet->setCellValue('H' . $row, isset($tanggal_cuti[0]) ? $tanggal_cuti[0] : '');
                        $sheet->setCellValue('I' . $row, isset($tanggal_cuti[1]) ? $tanggal_cuti[1] : '');
                        $sheet->setCellValue('J' . $row, isset($tanggal_cuti[2]) ? $tanggal_cuti[2] : '');
                        $sheet->setCellValue('K' . $row, isset($tanggal_cuti[3]) ? $tanggal_cuti[3] : '');
                        $sheet->setCellValue('L' . $row, isset($tanggal_cuti[4]) ? $tanggal_cuti[4] : '');
                        $sheet->setCellValue('M' . $row, isset($tanggal_cuti[5]) ? $tanggal_cuti[5] : '');
                        $sheet->setCellValue('N' . $row, $c->karyawan->sisa_cuti_pribadi.' Hari');
                    }
                }
            }
        }

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=data-cuti-export.xlsx');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit();
    }
}
