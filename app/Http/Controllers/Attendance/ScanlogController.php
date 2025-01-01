<?php

namespace App\Http\Controllers\Attendance;

use Throwable;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\Attendance\Device;
use App\Models\Attendance\Scanlog;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
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

    /**
     * Store a newly created resource in storage.
     */
    public function download_scanlog(Request $request)
    {
        $dataValidate = [
            'start_date' => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:end_date'],
            'end_date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date', function ($attribute, $value, $fail) {
                $startDate = request()->input('start_date');
                $endDate = request()->input('end_date');
                $diff = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
                if ($diff > 1) {
                    $fail('The end date must be within 2 days of the start date.');
                }
            }],
            'device_id' => ['required', 'integer', 'exists:attendance_devices,id_device'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        DB::beginTransaction();
        try {
            $device = Device::find($request->device_id);
            $organisasi_id = auth()->user()->organisasi_id;
            $cloudId = $device->cloud_id;
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            //CEK APAKAH SCANLOG SUDAH ADA DI TANGGAL TERSEBUT
            $scanlog = Scanlog::where('device_id', $request->device_id)->where(function($query) use ($startDate, $endDate){
                $query->where(function($query) use ($startDate, $endDate){
                    $query->whereDate('scan_date', $startDate)
                        ->orWhereDate('scan_date', $endDate);
                });
            });

            if($scanlog->exists()){
                $scanlog->delete();
            }

            //GET DATA FROM FINGERSPOT API
            $client = new Client();
            $url = 'https://developer.fingerspot.io/api/get_attlog';

            $body = [
                'trans_id' => '1',
                'cloud_id' => $cloudId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];
            
            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer ". env('API_TOKEN_FINGERSPOT'),
            ];

            $response = $client->post($url, [
                'headers' => $headers,
                'json' => $body,
            ]);
            $responseBody = $response->getBody();
            $response = json_decode($responseBody, true);
            $datas = [];

            if(!empty($response)){
                foreach($response['data'] as $data){
                    $datas[] = [
                        'pin' => $data['pin'],
                        'scan_date' => $data['scan_date'],
                        'scan_status' => $data['status_scan'],
                        'verify' => $data['verify'],
                        'device_id' => $request->device_id,
                        'organisasi_id' => $organisasi_id,
                        'start_date_scan' => $startDate,
                        'end_date_scan' => $endDate,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            } else {
                DB::rollBack();
                return response()->json(['message' => 'Data Scanlog tidak tersedia'], 400);
            }

            Scanlog::insert($datas);
            DB::commit();
            return response()->json(['message' => 'Data Scanlog Berhasil Diunduh!'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'karyawans.nama',
            1 => 'attendance_scanlogs.pin',
            2 => 'attendance_scanlogs.scan_date',
            3 => 'attendance_scanlogs.verify',
        );

        $totalData = Scanlog::count();
        $totalFiltered = $totalData;

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
                    $nestedData['verify'] = '<i class="fas fa-times"></i> Kosong';
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

    public function export_scanlog(Request $request){

        $dataValidate = [
            'start_date' => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:end_date'],
            'end_date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date', function ($attribute, $value, $fail) {
                $startDate = request()->input('start_date');
                $endDate = request()->input('end_date');
                $diff = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
                if ($diff > 1) {
                    $fail('The end date must be within 2 days of the start date.');
                }
            }],
            'device_id' => ['required', 'integer', 'exists:attendance_devices,id_device'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }
        try {
            $device = Device::find($request->device_id);
            $scanlogs = Scanlog::select(
                'karyawans.nama as karyawan',
                'attendance_scanlogs.pin',
                'attendance_scanlogs.scan_date',
                'attendance_scanlogs.verify',
            );
    
            $scanlogs->leftJoin('karyawans', 'karyawans.pin','attendance_scanlogs.pin');
            $scanlogs->leftJoin('users', 'users.id','karyawans.user_id');
    
            $scanlogs->where('attendance_scanlogs.organisasi_id', auth()->user()->organisasi_id);
            $scanlogs->where('users.organisasi_id', auth()->user()->organisasi_id);
    
            $scanlogs_clone = clone $scanlogs;

            $scanlogs_start = $scanlogs_clone->whereDate('attendance_scanlogs.scan_date', $request->start_date)->get();
            $scanlogs_end = $scanlogs->whereDate('attendance_scanlogs.scan_date', $request->end_date)->get();

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
                'NAMA',
                'PIN',
                'SCAN DATE',
                'DATE',
                'HOUR',
                'VERIFY'
            ];

    
            if($scanlogs_start->count() > 0){
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle($request->start_date);
    
                $row = 1;
                $col = 'A';
    
                foreach ($headers as $header) {
                    $sheet->setCellValue($col . '1', $header);
                    $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
                    $col++;
                }
    
                $row = 2;
    
                $columns = range('A', 'F');
                foreach ($columns as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
                $sheet->setAutoFilter('A1:F1');

                foreach ($scanlogs_start as $data) {
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

                    $sheet->setCellValue('A' . $row, $data->karyawan);
                    $sheet->setCellValue('B' . $row, $data->pin);
                    $sheet->setCellValue('C' . $row, $data->scan_date);
                    $sheet->setCellValue('D' . $row, Carbon::parse($data->scan_date)->format('Y-m-d'));
                    $sheet->setCellValue('E' . $row, Carbon::parse($data->scan_date)->format('H:i'));
                    $sheet->setCellValue('F' . $row, $verify);
                    $row++;
                }
            }

            if($request->start_date !== $request->end_date){
                if($scanlogs_end->count() > 0){
                    $sheet2 = $spreadsheet->createSheet();
                    $sheet2->setTitle($request->end_date);
        
                    $row2 = 1;
                    $col2 = 'A';
        
                    foreach ($headers as $header) {
                        $sheet2->setCellValue($col2 . '1', $header);
                        $sheet2->getStyle($col2 . '1')->applyFromArray($fillStyle);
                        $col2++;
                    }
        
                    $row2 = 2;
        
                    $columns2 = range('A', 'F');
                    foreach ($columns2 as $column) {
                        $sheet2->getColumnDimension($column)->setAutoSize(true);
                    }
                    $sheet2->setAutoFilter('A1:F1');
    
                    foreach ($scanlogs_end as $data) {
                        if ($data->verify == '1') {
                            $verify2 = 'Finger';
                        } elseif ($data->verify == '2') {
                            $verify2 = 'Password';
                        } elseif ($data->verify == '3') {
                            $verify2 = 'Card';
                        } elseif ($data->verify == '4') {
                            $verify2 = 'Face';
                        } elseif ($data->verify == '5') {
                            $verify2 = 'GPS';
                        } elseif ($data->verify == '6') {
                            $verify2 = 'Vein';
                        } else {
                            $verify2 = 'Kosong';
                        }
    
                        $sheet2->setCellValue('A' . $row2, $data->karyawan);
                        $sheet2->setCellValue('B' . $row2, $data->pin);
                        $sheet2->setCellValue('C' . $row2, $data->scan_date);
                        $sheet2->setCellValue('D' . $row2, Carbon::parse($data->scan_date)->format('Y-m-d'));
                        $sheet2->setCellValue('E' . $row2, Carbon::parse($data->scan_date)->format('H:i'));
                        $sheet2->setCellValue('F' . $row2, $verify2);
                        $row2++;
                    }
                }
            }
    
            $writer = new Xlsx($spreadsheet);
    
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename=Scanlog-'.$device->device_name.'-'.$request->start_date.'-'.$request->end_date.'.xlsx');
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
