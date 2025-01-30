<?php

namespace App\Http\Controllers\Attendance;

use Throwable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Attendance\ScanlogDetail;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RekapController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Attendance-E - Rekap",
            'page' => 'attendance-rekap',
        ];
        return view('pages.attendance-e.rekap.index', $dataPage);
    }

    public function export_rekap(Request $request)
    {
        $dataValidate = [
            'start' => ['required', 'date_format:Y-m-d'],
            'end' => ['required', 'date_format:Y-m-d', 'after_or_equal:start'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $start = $request->start;
        $end = $request->end;
        $dataFilter = [];
        $dataFilter['start'] = $start;
        $dataFilter['end'] = $end;
        $dataFilter['organisasi_id'] = auth()->user()->organisasi_id;

        $spreadsheet = new Spreadsheet();

        $fillStyle = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFF00',
                ],
            ],
        ];

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('REKAP PRESENSI');

        $row = 1;
        $col = 'A';
        $headers = [
            'NIK',
            'NAMA',
            'DEPT',
            'PIN',
            'H',
            'C',
            'I',
            'S',
            'TK',
            'CUTI BERSAMA',
            'HARI KERJA'
        ];

        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
            $col++;
        }

        $columns = range('A', 'K');
        foreach ($columns as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->setAutoFilter('A1:K1');
        $hari_kerja = Carbon::parse($start)->diffInWeekDays(Carbon::parse($end)) + 1;
        
        try {

            $datas = ScanlogDetail::rekapSummary($dataFilter);
            $row = 2;
            if($datas){
                foreach ($datas as $index => $data){
                    $jumlah_tanpa_keterangan = ($hari_kerja - $data->jumlah_cuti_bersama) - ($data->jumlah_hadir + $data->jumlah_cuti + $data->jumlah_izin + $data->jumlah_sakit);
                    $sheet->setCellValue('A' . $row, $data->ni_karyawan);
                    $sheet->setCellValue('B' . $row, $data->nama);
                    $sheet->setCellValue('C' . $row, $data->departemen);
                    $sheet->setCellValue('D' . $row, $data->pin);
                    $sheet->setCellValue('E' . $row, $data->jumlah_hadir);
                    $sheet->setCellValue('F' . $row, $data->jumlah_cuti);
                    $sheet->setCellValue('G' . $row, $data->jumlah_izin);
                    $sheet->setCellValue('H' . $row, $data->jumlah_sakit);
                    $sheet->setCellValue('I' . $row, $jumlah_tanpa_keterangan > 0 ? $jumlah_tanpa_keterangan : 0);
                    $sheet->setCellValue('J' . $row, $data->jumlah_cuti_bersama);
                    $sheet->setCellValue('K' . $row, $hari_kerja);
                    $row++;

                    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                }
            }

            $writer = new Xlsx($spreadsheet);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Rekap Presensi -'.Carbon::parse($start)->format('d-m-Y').' s/d '.Carbon::parse($end)->format('d-m-Y').'.xlsx"');
            header('Cache-Control: max-age=0');

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            exit();
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
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
}
