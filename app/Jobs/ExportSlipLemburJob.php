<?php

namespace App\Jobs;

use Exception;
use Carbon\Carbon;
use App\Models\Karyawan;
use App\Models\DetailLembur;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Queue\InteractsWithQueue;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Foundation\Queue\Queueable;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExportSlipLemburJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $periode, $organisasi_id, $departemen, $departemen_id, $pembagi_upah_lembur_harian, $start, $end, $export_slip_lembur;
    public $timeout = 1800;

    /**
     * Create a new job instance.
     */
    public function __construct($periode, $organisasi_id, $departemen, $departemen_id, $pembagi_upah_lembur_harian, $start, $end, $export_slip_lembur)
    {
        $this->periode = $periode;
        $this->organisasi_id = $organisasi_id;
        $this->departemen = $departemen;
        $this->departemen_id = $departemen_id;
        $this->pembagi_upah_lembur_harian = $pembagi_upah_lembur_harian;
        $this->start = $start;
        $this->end = $end;
        $this->export_slip_lembur = $export_slip_lembur;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try{
            $month = Carbon::createFromFormat('Y-m', $this->periode)->format('m');
            $year = Carbon::createFromFormat('Y-m', $this->periode)->format('Y');
            //CREATE EXCEL FILE
            activity('export_slip_lembur_start')->log('Start Export Slip Lembur'.$this->departemen.' - '.Carbon::createFromFormat('Y-m', $this->periode)->format('F Y'));
            $spreadsheet = new Spreadsheet();

            $fillStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
            ];
            
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('SLIP LEMBUR');
            $row = 1;
            $headers = [
                'NO',
                'HARI',
                'TANGGAL',
                'JAM MASUK',
                'JAM KELUAR',
                'JAM ISTIRAHAT',
                'JAM KELUAR SETELAH ISTIRAHAT',
                'TOTAL JAM',
                'KONVERSI JAM',
                'UANG MAKAN',
                'UPAH LEMBUR PERJAM',
                'JUMLAH'
            ];
            
            $members = Karyawan::select('karyawans.id_karyawan', 'karyawans.nama', 'karyawans.ni_karyawan')
            ->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
            ->leftJoin('detail_lemburs', 'karyawans.id_karyawan', 'detail_lemburs.karyawan_id')
            ->when($this->departemen_id, function ($query) {
                $query->where('posisis.departemen_id', $this->departemen_id);
            })
            ->where('detail_lemburs.organisasi_id', $this->organisasi_id)
            ->whereMonth('detail_lemburs.aktual_mulai_lembur', $month)
            ->whereYear('detail_lemburs.aktual_mulai_lembur', $year)
            ->whereHas('lembur', function ($query) {
                $query->where('status', 'COMPLETED')
                      ->whereNotNull('actual_legalized_by');
            })
            ->groupBy('karyawans.id_karyawan', 'karyawans.nama', 'karyawans.ni_karyawan')
            ->get();
            
            $columns = range('A', 'M');
            foreach ($columns as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
    
            $last_row = 0;
            $total_nominal_slip_lembur = 0;
            foreach ($members as $kry){
                // TEXT "SLIP LEMBUR BULAN INI"
                $sheet->mergeCells('A'.$row.':F'.$row+1);
                $sheet->setCellValue('A'.$row, 'SLIP LEMBUR BULAN '.Carbon::createFromFormat('Y-m', $this->periode)->format('F Y'));
                $sheet->getStyle('A'.$row.':F'.$row+1)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FF808080',
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                ]);

                $row += 2;
                $sheet->setCellValue('B'.$row, 'NAMA');
                $sheet->setCellValue('C'.$row, ':');
                $sheet->setCellValue('D'.$row, $kry->nama);
                $sheet->setCellValue('B'.$row+1, 'NIK');
                $sheet->setCellValue('C'.$row+1, ':');
                $sheet->setCellValue('D'.$row+1, $kry->ni_karyawan);
                $sheet->getStyle('B'.$row.':B'.$row+1)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                ]);

                $sheet->getStyle('C'.$row.':C'.$row+1)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                ]);

                $sheet->getStyle('D'.$row.':D'.$row+1)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                ]);

                $row += 2;
                $col = 'A';
                foreach ($headers as $header) {
                    $sheet->setCellValue($col . $row, $header);
                    $sheet->mergeCells($col . $row.':' . $col . ($row+1));
                    $sheet->getStyle($col . $row.':' . $col . ($row+1))->applyFromArray($fillStyle);
                    $col++;
                }
                
                $row += 2;
                //LOOPING AWAL SAMPAI AKHIR BULAN
                $total_jam = 0;
                $total_konversi_jam = 0;
                $total_uang_makan = 0;
                $total_spl = 0;
                for($i = 0; $i <= Carbon::parse($this->start)->diffInDays(Carbon::parse($this->end)); $i++){
                    $date = Carbon::parse($this->start)->addDays($i)->toDateString();
                    $slipLemburs = DetailLembur::getSlipLemburPerDepartemen($kry->id_karyawan, $date, $this->organisasi_id);

                    if($slipLemburs->count() > 0){
                        foreach($slipLemburs as $index => $slipLembur){
                            $upah_lembur_per_jam = $slipLembur ? $slipLembur->gaji_lembur / $slipLembur->pembagi_upah_lembur : 0;
                            $total_jam += $slipLembur->durasi;
                            $total_konversi_jam += $slipLembur->durasi_konversi_lembur;
                            $total_uang_makan += $slipLembur->uang_makan;
                            $total_spl += $slipLembur->nominal;
                            $total_nominal_slip_lembur += $slipLembur->nominal;
                            $sheet->setCellValue('A'.$row, $i+1);
                            $sheet->setCellValue('B'.$row, Carbon::parse($date)->locale('id')->translatedFormat('l'));
        
                            //JIKA WEEKEND UBAH STYLE CELL
                            if(Carbon::parse($date)->isWeekend()){
                                $sheet->getStyle('B'.$row)->applyFromArray([
                                    'fill' => [
                                        'fillType' => Fill::FILL_SOLID,
                                        'startColor' => [
                                            'argb' => 'FFFF0000',
                                        ],
                                    ],
                                    'font' => [
                                        'color' => [
                                            'argb' => 'FFFFFFFF',
                                        ],
                                    ],
                                ]);
                            }

                            if($slipLembur->keterangan){
                                if (substr($slipLembur->keterangan, 0, 6) === 'BYPASS') {
                                    $keterangan = substr($slipLembur->keterangan, 7);
                                } else {
                                    $keterangan = '';
                                }
                            } else {
                                $keterangan = '';
                            }
        
                            $sheet->setCellValue('C'.$row, Carbon::parse($date)->format('d-m-Y'));
                            $sheet->setCellValue('D'.$row, Carbon::parse($slipLembur->aktual_mulai_lembur)->format('H:i'));
                            $sheet->setCellValue('E'.$row, Carbon::parse($slipLembur->aktual_selesai_lembur)->format('H:i'));
                            $sheet->setCellValue('F'.$row, number_format($slipLembur->durasi_istirahat / 100 , 2));
                            $sheet->setCellValue('G'.$row, Carbon::parse($slipLembur->aktual_selesai_lembur)->subMinutes($slipLembur->durasi_istirahat)->format('H:i'));
                            $sheet->setCellValue('H'.$row, number_format($slipLembur->durasi / 60, 2));
                            $sheet->setCellValue('I'.$row, number_format($slipLembur->durasi_konversi_lembur / 60, 2));
                            $sheet->setCellValueExplicit('J'.$row, $slipLembur->uang_makan, DataType::TYPE_NUMERIC);
                            $sheet->setCellValueExplicit('K'.$row, $upah_lembur_per_jam, DataType::TYPE_NUMERIC);
                            $sheet->setCellValueExplicit('L'.$row, $slipLembur->nominal, DataType::TYPE_NUMERIC);
                            $sheet->setCellValue('M'.$row, $keterangan);

                            //STYLE CELL
                            $sheet->getStyle('J'.$row)->getNumberFormat()->setFormatCode('[$Rp-421] #,##0');
                            $sheet->getStyle('K'.$row)->getNumberFormat()->setFormatCode('[$Rp-421] #,##0');
                            $sheet->getStyle('L'.$row)->getNumberFormat()->setFormatCode('[$Rp-421] #,##0');
                            $sheet->getStyle('C'.$row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                ],
                            ]);
                            $sheet->getStyle('J'.$row.':K'.$row)->applyFromArray([
                                'font' => [
                                    'color' => [
                                        'argb' => 'FFFF0000',
                                    ],
                                ],
                            ]);
                            $sheet->getStyle('A'.$row.':L'.$row)->applyFromArray([
                                'borders' => [
                                    'allBorders' => [
                                        'borderStyle' => Border::BORDER_THIN,
                                        'color' => ['argb' => 'FF000000'],
                                    ],
                                ],
                            ]);

                            if ($slipLemburs->count() > 1 && $index == 0) {
                                //STYLE CELL
                                $sheet->getStyle('C'.$row)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                    ],
                                ]);
                                $sheet->getStyle('J'.$row.':K'.$row)->applyFromArray([
                                    'font' => [
                                        'color' => [
                                            'argb' => 'FFFF0000',
                                        ],
                                    ],
                                ]);
                                $sheet->getStyle('A'.$row.':K'.$row)->applyFromArray([
                                    'borders' => [
                                        'allBorders' => [
                                            'borderStyle' => Border::BORDER_THIN,
                                            'color' => ['argb' => 'FF000000'],
                                        ],
                                    ],
                                ]);
                                $row++;
                            }
                        }
                    } else {
                        $sheet->setCellValue('A'.$row, $i+1);
                        $sheet->setCellValue('B'.$row, Carbon::parse($date)->locale('id')->translatedFormat('l'));

                        //JIKA WEEKEND UBAH STYLE CELL
                        if(Carbon::parse($date)->isWeekend()){
                            $sheet->getStyle('B'.$row)->applyFromArray([
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => [
                                        'argb' => 'FFFF0000',
                                    ],
                                ],
                                'font' => [
                                    'color' => [
                                        'argb' => 'FFFFFFFF',
                                    ],
                                ],
                            ]);
                        }

                        $sheet->setCellValue('C'.$row, Carbon::parse($date)->format('d-m-Y'));
                        $sheet->setCellValue('D'.$row, '-');
                        $sheet->setCellValue('E'.$row, '-');
                        $sheet->setCellValue('F'.$row, '-');
                        $sheet->setCellValue('G'.$row, '-');
                        $sheet->setCellValue('H'.$row, '-');
                        $sheet->setCellValue('I'.$row, '-');
                        $sheet->setCellValueExplicit('J'.$row, (int)0, DataType::TYPE_NUMERIC);
                        $sheet->setCellValueExplicit('K'.$row, (int)0, DataType::TYPE_NUMERIC);
                        $sheet->setCellValueExplicit('L'.$row, (int)0, DataType::TYPE_NUMERIC);
                        $sheet->setCellValue('M'.$row, '');
                    }

                    //STYLE CELL
                    $sheet->getStyle('J'.$row)->getNumberFormat()->setFormatCode('[$Rp-421] #,##0');
                    $sheet->getStyle('K'.$row)->getNumberFormat()->setFormatCode('[$Rp-421] #,##0');
                    $sheet->getStyle('L'.$row)->getNumberFormat()->setFormatCode('[$Rp-421] #,##0');
                    $sheet->getStyle('C'.$row)->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                    $sheet->getStyle('J'.$row.':K'.$row)->applyFromArray([
                        'font' => [
                            'color' => [
                                'argb' => 'FFFF0000',
                            ],
                        ],
                    ]);
                    $sheet->getStyle('A'.$row.':L'.$row)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'FF000000'],
                            ],
                        ],
                    ]);

                    $row++;
                }

                $sheet->setCellValue('G'.$row, $kry->nama);    
                $sheet->setCellValue('H'.$row, number_format($total_jam / 60 , 2));    
                $sheet->setCellValue('I'.$row, number_format($total_konversi_jam / 60 , 2));    
                $sheet->setCellValueExplicit('J'.$row, $total_uang_makan, DataType::TYPE_NUMERIC);    
                $sheet->setCellValue('K'.$row, '-');    
                $sheet->setCellValueExplicit('L'.$row, $total_spl, DataType::TYPE_NUMERIC);
                $sheet->setCellValue('K'.$row+1, 'SESUAI SPL');
                $sheet->setCellValueExplicit('L'.$row+1, $total_spl, DataType::TYPE_NUMERIC);

                $sheet->getStyle('J'.$row)->getNumberFormat()->setFormatCode('[$Rp-421] #,##0');
                $sheet->getStyle('L'.$row)->getNumberFormat()->setFormatCode('[$Rp-421] #,##0');
                $sheet->getStyle('L'.($row+1))->getNumberFormat()->setFormatCode('[$Rp-421] #,##0');
                $sheet->getStyle('G'.$row.':L'.$row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ]
                ]);
                $sheet->getStyle('K'.($row+1).':L'.($row+1))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                
                $row += 6;
                $last_row = $row;
            }

            $sheet->setCellValue('K'.$last_row, 'TOTAL SLIP LEMBUR');
            $sheet->setCellValueExplicit('L'.$last_row, $total_nominal_slip_lembur, DataType::TYPE_NUMERIC);
            $sheet->getStyle('L'.$row)->getNumberFormat()->setFormatCode('[$Rp-421] #,##0');
            $sheet->getStyle('K'.($last_row).':L'.($last_row))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            $writer = new Xlsx($spreadsheet);
            $now = time();
            $filename = $now.'- Slip Pembayaran Lembur -'.$this->departemen.' - '.Carbon::createFromFormat('Y-m', $this->periode)->format('F Y').'.xlsx';
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $directory = storage_path('app/public/export/slip_lembur/');
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }
            $writer->save($directory.$filename);
            activity('export_slip_lembur_end')->log($filename);
            $this->export_slip_lembur->update([
                'status' => 'CO',
                'message' => 'Export Slip Lembur Berhasil',
                'attachment' => 'export/slip_lembur/'.$filename
            ]);
        } catch (Exception $e) {
            activity('export_slip_lembur_error')->log($e->getMessage());
            $this->export_slip_lembur->update([
                'status' => 'FL',
                'message' => $e->getMessage()
            ]);
        }
    }
}
