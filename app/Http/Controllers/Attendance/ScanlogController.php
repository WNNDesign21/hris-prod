<?php

namespace App\Http\Controllers\Attendance;

use Throwable;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Jobs\DownloadScanlogJob;
use App\Models\Attendance\Device;
use App\Models\Attendance\Scanlog;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Jobs\SummarizeAttendanceJob;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\Attendance\AttendanceSummary;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ScanlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $devices = Device::where('organisasi_id', auth()->user()->organisasi_id)->get();
        $dataPage = [
            'pageTitle' => "Attendance-E - Scanlog",
            'page' => 'attendance-scanlog',
            'devices' => $devices,
        ];
        return view('pages.attendance-e.scanlog.index', $dataPage);
    }

    // private function get_att_scanlog($organisasi_id, $device_id, $cloudId, $startDate, $endDate)
    // {
    //     DB::beginTransaction();
    //     //CEK APAKAH SCANLOG SUDAH ADA DI TANGGAL TERSEBUT
    //     $scanlog = Scanlog::where('device_id', $device_id)->where(function($query) use ($startDate, $endDate){
    //         $query->where(function($query) use ($startDate, $endDate){
    //             $query->whereDate('scan_date', $startDate)
    //                 ->orWhereDate('scan_date', $endDate);
    //         });
    //     })->whereIn('verify', [1, 2, 3, 4, 6]);

    //     if($scanlog->exists()){
    //         $scanlog->delete();
    //     }

    //     //GET DATA FROM FINGERSPOT API
    //     $client = new Client();
    //     $url = 'https://developer.fingerspot.io/api/get_attlog';

    //     $body = [
    //         'trans_id' => '1',
    //         'cloud_id' => $cloudId,
    //         'start_date' => $startDate,
    //         'end_date' => $endDate,
    //     ];

    //     $headers = [
    //         'Content-Type' => 'application/json',
    //         'Authorization' => "Bearer ". env('API_TOKEN_FINGERSPOT'),
    //     ];

    //     $response = $client->post($url, [
    //         'headers' => $headers,
    //         'json' => $body,
    //     ]);
    //     $responseBody = $response->getBody();
    //     $response = json_decode($responseBody, true);
    //     $datas = [];
    //     $pins = [];
    //     $scanlog_datas = [];

    //     if(!empty($response)){
    //         foreach($response['data'] as $data){
    //             if(!in_array($data['pin'], $pins)){
    //                 $pins[] = $data['pin'];
    //             }

    //             $datas[] = [
    //                 'pin' => $data['pin'],
    //                 'scan_date' => $data['scan_date'],
    //                 'scan_status' => $data['status_scan'],
    //                 'verify' => $data['verify'],
    //                 'device_id' => $device_id,
    //                 'organisasi_id' => $organisasi_id,
    //                 'start_date_scan' => $startDate,
    //                 'end_date_scan' => $endDate,
    //                 'created_at' => now(),
    //                 'updated_at' => now()
    //             ];
    //         }

    //         Scanlog::insert($datas);
    //         DB::commit();

    //         $newScanlog = Scanlog::where('device_id', $device_id)->where(function($query) use ($startDate, $endDate){
    //             $query->where(function($query) use ($startDate, $endDate){
    //                 $query->whereDate('scan_date', $startDate)
    //                     ->orWhereDate('scan_date', $endDate);
    //             });
    //         })->pluck('pin')->toArray();

    //         //Execute Job
    //         if ($startDate == $endDate) {
    //             SummarizeAttendanceJob::dispatch($newScanlog, $organisasi_id, auth()->user(), $startDate);
    //         } else {
    //             SummarizeAttendanceJob::dispatch($newScanlog, $organisasi_id, auth()->user(), $startDate);
    //             SummarizeAttendanceJob::dispatch($newScanlog, $organisasi_id, auth()->user(), $endDate);
    //         }
    //         return true;
    //     } else {
    //         DB::rollBack();
    //         return false;
    //     }
    // }

    private function get_att_scanlog($organisasi_id, $device_id, $cloudId, $startDate, $endDate)
    {
        DB::beginTransaction();

        try {
            // 0️⃣ Ambil device dari DB
            $device = Device::find($device_id);
            if (!$device || !$device->device_sn) {
                DB::rollBack();
                \Log::error("[get_att_scanlog] GAGAL MENCARI DEVICE: device_id={$device_id}, SN tidak ditemukan");
                return false;
            }
            $device_sn = $device->device_sn;
            \Log::info("[get_att_scanlog] Mulai proses | Device SN={$device_sn}, Range={$startDate} s/d {$endDate}");

            // 1️⃣ Hapus data lama
            $deleted = Scanlog::where('device_id', $device_id)
                ->whereBetween('scan_date', [$startDate, $endDate])
                ->whereIn('verify', [0, 1, 2, 3, 4, 5, 6])
                ->delete();
            \Log::info("[get_att_scanlog] Data lama dihapus: {$deleted} record");

            // 2️⃣ Ambil data dari SQL Server (mesin fingerprint)
            $rawData = DB::connection('sqlsrv')
                ->table('USERINFO')
                ->join('CHECKINOUT', 'USERINFO.USERID', '=', 'CHECKINOUT.USERID')
                ->select(
                    'USERINFO.USERID',
                    'USERINFO.BADGENUMBER',
                    'CHECKINOUT.CHECKTIME',
                    'CHECKINOUT.VERIFYCODE',
                    'CHECKINOUT.sn',
                    'CHECKINOUT.CHECKTYPE'
                )
                ->where('CHECKINOUT.sn', $device_sn)
                ->whereBetween('CHECKINOUT.CHECKTIME', [$startDate, $endDate])
                ->get();

            \Log::info("[get_att_scanlog] Data dari SQL Server: {$rawData->count()} record ditemukan");

            if ($rawData->isEmpty()) {
                DB::rollBack();
                \Log::warning("[get_att_scanlog] Tidak ada data scanlog ditemukan | SN={$device_sn}, Range={$startDate} - {$endDate}");
                return false;
            }

            // 3️⃣ Transform data siap insert
            $dataToInsert = $rawData->map(function ($item) use ($device_id, $organisasi_id, $startDate, $endDate) {
                return [
                    'pin' => $item->BADGENUMBER,
                    'scan_date' => $item->CHECKTIME,
                    'scan_status' => $item->CHECKTYPE === 'I' ? 0 : 1,
                    'verify' => $item->VERIFYCODE,
                    'device_id' => $device_id,
                    'organisasi_id' => $organisasi_id,
                    'start_date_scan' => $startDate,
                    'end_date_scan' => $endDate,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            \Log::info("[get_att_scanlog] Data siap insert: " . count($dataToInsert) . " record");

            // 4️⃣ Insert batch ke tabel Scanlog
            Scanlog::insert($dataToInsert);
            \Log::info("[get_att_scanlog] Insert berhasil ke attendance_scanlogs");

            // 5️⃣ Commit transaksi
            DB::commit();

            // 6️⃣ Ambil pin untuk proses summarize
            $newScanlog = Scanlog::where('device_id', $device_id)
                ->whereBetween('scan_date', [$startDate, $endDate])
                ->pluck('pin')
                ->toArray();

            \Log::info("[get_att_scanlog] Jumlah pin untuk summarize: " . count($newScanlog));

            if ($startDate === $endDate) {
                SummarizeAttendanceJob::dispatch($newScanlog, $organisasi_id, auth()->user(), $startDate);
                \Log::info("[get_att_scanlog] SummarizeAttendanceJob dispatched untuk {$startDate}");
            } else {
                SummarizeAttendanceJob::dispatch($newScanlog, $organisasi_id, auth()->user(), $startDate);
                SummarizeAttendanceJob::dispatch($newScanlog, $organisasi_id, auth()->user(), $endDate);
                \Log::info("[get_att_scanlog] SummarizeAttendanceJob dispatched untuk {$startDate} dan {$endDate}");
            }

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("[get_att_scanlog] ERROR: {$e->getMessage()} | File={$e->getFile()} | Line={$e->getLine()}");
            return false;
        }
    }

    public function download_scanlog(Request $request)
    {
        $dataValidate = [
            'start_date' => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:end_date'],
            'end_date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'device_id' => ['required', 'integer', 'exists:attendance_devices,id_device']
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            \Log::error('[download_scanlog] Gagal validasi: ' . json_encode($errors));
            return response()->json(['message' => $errors], 402);
        }

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        \Log::info('[download_scanlog] Mulai | start=' . $startDate->toDateTimeString() . ' end=' . $endDate->toDateTimeString() . ' device_id=' . $request->device_id . ' user_id=' . auth()->id());

        if ($startDate->year !== $endDate->year || $startDate->month !== $endDate->month) {
            \Log::warning('[download_scanlog] Validasi gagal: bulan/tahun berbeda | start=' . $startDate->toDateString() . ' end=' . $endDate->toDateString());
            return response()->json(['message' => 'Tanggal mulai dan tanggal akhir harus berada di bulan dan tahun yang sama.'], 402);
        }

        try {
            $device = Device::find($request->device_id);
            $organisasi_id = auth()->user()->organisasi_id;
            $cloudId = $device->cloud_id;

            \Log::info('[download_scanlog] Device ditemukan | id_device=' . $device->id_device . ' cloud_id=' . $cloudId);

            $diff_date = $startDate->diffInDays($endDate);

            if ($diff_date <= 1) {
                \Log::info('[download_scanlog] Mode 1 hari | range=' . $request->start_date . ' - ' . $request->end_date);

                $result = $this->get_att_scanlog(
                    $organisasi_id,
                    $request->device_id,
                    $cloudId,
                    $request->start_date . " 00:00:00",
                    $request->end_date . " 23:59:59"
                );

                if (!$result) {
                    \Log::warning('[download_scanlog] get_att_scanlog return FALSE untuk tanggal ' . $request->start_date);
                } else {
                    \Log::info('[download_scanlog] get_att_scanlog sukses untuk tanggal ' . $request->start_date);
                }

            } else {
                \Log::info('[download_scanlog] Mode rentang panjang | range=' . $request->start_date . ' - ' . $request->end_date);

                $summarizeExists = AttendanceSummary::whereMonth('periode', $startDate->month)
                    ->whereYear('periode', $startDate->year)
                    ->exists();

                \Log::info('[download_scanlog] Cek summarizeExists: ' . ($summarizeExists ? 'true' : 'false'));

                if (!$summarizeExists) {
                    \Log::info('[download_scanlog] Summarize belum ada, ambil scanlog pertama');
                    $this->get_att_scanlog($organisasi_id, $request->device_id, $cloudId, $startDate->format('Y-m-d'), $startDate->format('Y-m-d'));
                }

                $dateRanges = [];
                for ($currentStartDate = $startDate->copy(); $currentStartDate->lte($endDate); $currentStartDate->addDays(2)) {
                    $currentEndDate = $currentStartDate->copy()->addDay();
                    if ($currentEndDate->gt($endDate)) {
                        $currentEndDate = $endDate;
                    }
                    $dateRanges[] = [
                        'start_date' => $currentStartDate->format('Y-m-d'),
                        'end_date' => $currentEndDate->format('Y-m-d'),
                    ];
                }

                \Log::info('[download_scanlog] Date ranges terbuat: ' . json_encode($dateRanges));

                foreach ($dateRanges as $dr) {
                    \Log::info('[download_scanlog] Dispatch DownloadScanlogJob | start=' . $dr['start_date'] . ' end=' . $dr['end_date']);
                    DownloadScanlogJob::dispatch($organisasi_id, $cloudId, $dr['start_date'], $dr['end_date'], $device->id_device, auth()->user());
                }
            }

            \Log::info('[download_scanlog] Selesai sukses');
            return response()->json(['message' => 'Data Scanlog Berhasil Diunduh!'], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            \Log::error("[download_scanlog] ERROR: {$e->getMessage()} | File={$e->getFile()} | Line={$e->getLine()}");
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'karyawans.nama',
            1 => 'attendance_scanlogs.pin',
            2 => 'attendance_scanlogs.verify',// pastikan sorting by date
            3 => DB::raw('attendance_scanlogs.scan_date'),
        );

        $totalData = Scanlog::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        // Jika order by scan_date, pastikan benar-benar order by attendance_scanlogs.scan_date (datetime)
        if ($request->input('order.0.column') == 2) {
            $order = DB::raw('attendance_scanlogs.scan_date');
        }
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

        $scanlogs = Scanlog::getData($dataFilter, $settings);
        $totalFiltered = Scanlog::countData($dataFilter);

        $dataTable = [];

        if (!empty($scanlogs)) {
            foreach ($scanlogs as $data) {
                if ($data->verify == '1') {
                    $nestedData['verify'] = '<i class="fas fa-fingerprint"></i> Finger';
                } elseif ($data->verify == '2') {
                    $nestedData['verify'] = '<i class="fas fa-unlock-alt"></i> Password';
                } elseif ($data->verify == '3') {
                    $nestedData['verify'] = '<i class="fas fa-id-card"></i> Card';
                } elseif ($data->verify == '4') {
                    $nestedData['verify'] = '<i class="fas fa-laugh-beam"></i> Face';
                } elseif ($data->verify == '5') {
                    $nestedData['verify'] = '<i class="fas fa-map-marker-alt"></i> GPS';
                } elseif ($data->verify == '6') {
                    $nestedData['verify'] = '<i class="fas fa-user"></i> Vein';
                } else {
                    $nestedData['verify'] = '<i class="fas fa-laugh-beam"></i> Face';
                }

                $nestedData['karyawan'] = $data->karyawan;
                $nestedData['pin'] = $data->pin;
                $nestedData['scan_date'] = Carbon::parse($data->scan_date)->format('d M Y, H:i');

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

    public function export_scanlog(Request $request)
    {

        $dataValidate = [
            'start_date' => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:end_date'],
            'end_date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'device_id' => ['required', 'integer', 'exists:attendance_devices,id_device'],
            'format' => ['required', 'in:H,V'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }
        try {
            $device = Device::find($request->device_id);
            $organisasi_id = auth()->user()->organisasi_id;

            if ($request->format == 'V') {
                $scanlogs = Scanlog::select(
                    'karyawans.nama as karyawan',
                    'karyawans.ni_karyawan as ni_karyawan',
                    'attendance_scanlogs.pin',
                    'attendance_scanlogs.scan_date',
                    'attendance_scanlogs.verify',
                );

                $scanlogs->leftJoin('karyawans', 'karyawans.pin', 'attendance_scanlogs.pin')->where('karyawans.organisasi_id', $organisasi_id);
                $scanlogs->leftJoin('users', 'users.id', 'karyawans.user_id')->where('users.organisasi_id', $organisasi_id);

                $scanlogs->where('attendance_scanlogs.organisasi_id', $organisasi_id);
                $scanlogs->where('attendance_scanlogs.device_id', $request->device_id);
                $scanlogs->whereBetween(DB::raw('DATE(attendance_scanlogs.scan_date)'), [$request->start_date, $request->end_date]);
                // $scanlogs->where(function($query) use ($request){
                //     $query->whereDate('attendance_scanlogs.scan_date', $request->start_date)
                //         ->orWhereDate('attendance_scanlogs.scan_date', $request->end_date);
                // });
                $scanlogs->orderBy(DB::raw('karyawans.nama, DATE(attendance_scanlogs.scan_date)'), 'ASC');
                $scanlogs = $scanlogs->get();

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

                $headers = [
                    'NIK',
                    'NAMA',
                    'PIN',
                    'SCAN DATE',
                    'DATE',
                    'HOUR',
                    'VERIFY'
                ];


                if ($scanlogs->count() > 0) {
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->setTitle($request->start_date . ' - ' . $request->end_date);

                    $row = 1;
                    $col = 'A';

                    foreach ($headers as $header) {
                        $sheet->setCellValue($col . '1', $header);
                        $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
                        $col++;
                    }

                    $row = 2;

                    $columns = range('A', 'G');
                    foreach ($columns as $column) {
                        $sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                    $sheet->setAutoFilter('A1:G1');

                    foreach ($scanlogs as $data) {
                        // Filter: skip if both NIK and Nama are empty/null/whitespace
                        $nik = isset($data->ni_karyawan) ? trim($data->ni_karyawan) : '';
                        $nama = isset($data->karyawan) ? trim($data->karyawan) : '';
                        // Filter: skip if both NIK and Nama are NULL, empty, whitespace, or string 'NULL' (case-insensitive)
                        $nik_invalid = (is_null($data->ni_karyawan) || $nik === '' || strtoupper($nik) === 'NULL' || preg_match('/^\s*$/', $nik));
                        $nama_invalid = (is_null($data->karyawan) || $nama === '' || strtoupper($nama) === 'NULL' || preg_match('/^\s*$/', $nama));
                        if ($nik_invalid && $nama_invalid) {
                            continue;
                        }
                        if ($data->verify == '1') {
                            $verify = 'Finger';
                        } elseif ($data->verify == '2') {
                            $verify = 'Password';
                        } elseif ($data->verify == '3') {
                            $verify = 'Card';
                        } elseif ($data->verify == '4') {
                            $verify = 'Face';
                        } elseif ($data->verify == '5') {
                            $verify = 'GPS';
                        } elseif ($data->verify == '6') {
                            $verify = 'Vein';
                        } else {
                            $verify = 'Kosong';
                        }

                        $sheet->setCellValue('A' . $row, $data->ni_karyawan);
                        $sheet->setCellValue('B' . $row, $data->karyawan);
                        $sheet->setCellValue('C' . $row, $data->pin);
                        $sheet->setCellValue('D' . $row, $data->scan_date);
                        $sheet->setCellValue('E' . $row, Carbon::parse($data->scan_date)->format('Y-m-d'));
                        $sheet->setCellValue('F' . $row, Carbon::parse($data->scan_date)->format('H:i'));
                        $sheet->setCellValue('G' . $row, $verify);
                        $row++;
                    }
                }
            } else {
                $scanlogs = DB::select("
                    SELECT
                        s.pin,
                        s.nama AS karyawan,
                        s.ni_karyawan as nik,
                        DATE(s.scan_date) AS scan_date,
                        MAX(CASE WHEN rn = 1 THEN TO_CHAR(s.scan_date, 'HH24:MI') ELSE NULL END) AS scan_1,
                        MAX(CASE WHEN rn = 2 THEN TO_CHAR(s.scan_date, 'HH24:MI') ELSE NULL END) AS scan_2,
                        MAX(CASE WHEN rn = 3 THEN TO_CHAR(s.scan_date, 'HH24:MI') ELSE NULL END) AS scan_3,
                        MAX(CASE WHEN rn = 4 THEN TO_CHAR(s.scan_date, 'HH24:MI') ELSE NULL END) AS scan_4,
                        MAX(CASE WHEN rn = 5 THEN TO_CHAR(s.scan_date, 'HH24:MI') ELSE NULL END) AS scan_5,
                        MAX(CASE WHEN rn = 6 THEN TO_CHAR(s.scan_date, 'HH24:MI') ELSE NULL END) AS scan_6
                    FROM (
                        SELECT
                            attendance_scanlogs.pin,
                            k.nama,
                            k.ni_karyawan,
                            attendance_scanlogs.scan_date,
                            ROW_NUMBER() OVER (PARTITION BY attendance_scanlogs.pin, DATE(scan_date), attendance_scanlogs.organisasi_id ORDER BY scan_date) AS rn
                        FROM
                            attendance_scanlogs
                        LEFT JOIN
                            karyawans AS k ON k.pin = attendance_scanlogs.pin AND k.organisasi_id = $organisasi_id
                        LEFT JOIN
                            users ON users.id = k.user_id AND users.organisasi_id = $organisasi_id
                        WHERE
                            attendance_scanlogs.organisasi_id = $organisasi_id
                            AND attendance_scanlogs.device_id = $request->device_id
                            AND DATE(attendance_scanlogs.scan_date) BETWEEN '$request->start_date' AND '$request->end_date'
                    ) AS s
                    GROUP BY s.pin, DATE(s.scan_date), s.nama, s.ni_karyawan
                    ORDER BY s.nama, DATE(s.scan_date);
                ");

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

                $headers = [
                    'NIK',
                    'NAMA',
                    'PIN',
                    'DATE',
                    'SCAN 1',
                    'SCAN 2',
                    'SCAN 3',
                    'SCAN 4',
                    'SCAN 5',
                    'SCAN 6'
                ];

                if (count($scanlogs) > 0) {
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->setTitle($request->start_date . ' - ' . $request->end_date);

                    $row = 1;
                    $col = 'A';

                    foreach ($headers as $header) {
                        $sheet->setCellValue($col . '1', $header);
                        $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
                        $col++;
                    }

                    $row = 2;

                    $columns = range('A', 'J');
                    foreach ($columns as $column) {
                        $sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                    $sheet->setAutoFilter('A1:D1');

                    foreach ($scanlogs as $data) {
                        // Filter: skip if both NIK and Nama are empty/null/whitespace
                        $nik = isset($data->nik) ? trim($data->nik) : '';
                        $nama = isset($data->karyawan) ? trim($data->karyawan) : '';
                        // Filter: skip if both NIK and Nama are NULL, empty, whitespace, or string 'NULL' (case-insensitive)
                        $nik_invalid = (is_null($data->nik) || $nik === '' || strtoupper($nik) === 'NULL' || preg_match('/^\s*$/', $nik));
                        $nama_invalid = (is_null($data->karyawan) || $nama === '' || strtoupper($nama) === 'NULL' || preg_match('/^\s*$/', $nama));
                        if ($nik_invalid && $nama_invalid) {
                            continue;
                        }
                        $sheet->setCellValue('A' . $row, $data->nik);
                        $sheet->setCellValue('B' . $row, $data->karyawan);
                        $sheet->setCellValue('C' . $row, $data->pin);
                        $sheet->setCellValue('D' . $row, Carbon::parse($data->scan_date)->format('Y-m-d'));
                        $sheet->setCellValue('E' . $row, $data->scan_1);
                        $sheet->setCellValue('F' . $row, $data->scan_2);
                        $sheet->setCellValue('G' . $row, $data->scan_3);
                        $sheet->setCellValue('H' . $row, $data->scan_4);
                        $sheet->setCellValue('I' . $row, $data->scan_5);
                        $sheet->setCellValue('J' . $row, $data->scan_6);
                        $row++;
                    }
                }
            }

            $writer = new Xlsx($spreadsheet);

            $fileName = 'Scanlog-' . $device->device_name . '-' . $request->start_date . '-' . $request->end_date . '.xlsx';

            return response()->streamDownload(function () use ($spreadsheet) {
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save('php://output');
                $spreadsheet->disconnectWorksheets();
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0',
            ]);

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
