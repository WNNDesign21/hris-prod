<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use App\Helpers\Sto;
use App\Models\Cutie;
use GuzzleHttp\Client;
use App\Models\Karyawan;
use App\Helpers\Approval;
use App\Models\ApprovalCuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Attendance\ScanlogDetail;
use App\Helpers\SendWhatsappNotification;
use Illuminate\Support\Facades\Validator;
use App\Models\StockOpname\StockOpnameUpload;

class TestController extends Controller
{
    private function getOrgByWh($wh_id)
    {

        $data = DB::connection('idempiere')->table('m_warehouse')
            ->select('m_warehouse_id', 'ad_org_id')
            ->where('m_warehouse_id', $wh_id)->first();

        return $data->ad_org_id;
    }

    public function index()
    {
        $getUpload = StockOpnameUpload::all();



        $dataUploads = [];
        foreach ($getUpload as $key => $value) {
            $org_id = $this->getOrgByWh($value->wh_id);
            $dataUploads[] = [
                "AD_Client_ID" => [
                    "id" => 1000000,
                    "tableName" => "ad_client"
                ],
                "AD_Org_ID" => [
                    "id" => $org_id,
                    "tableName" => "ad_org"
                ],
                "M_Warehouse_ID" => [
                    "id" => $value->wh_id,
                    "tableName" => "m_warehouse"
                ],
                "M_Locator_ID" => [
                    "id" => $value->locator_id,
                    "tableName" => "m_locator"
                ],
                "Description" => "Sto Upload",
                "MovementDate" => $value->doc_date,
                "C_DocType_ID" => [
                    "id" => 1000023,
                    "tableName" => "c_doctype"
                ],
                "doc-action" => "CO",
            ];
        }


        return response()->json($dataUploads, 200);

        // $request = Sto::testLogin();

        // return response()->json($request, 200);

        // $data = Sto::testingFlow();
        // session(['token' => "jos jos kunyuk", 'refresh_token' => "jos jos kunyukkuruyuadsfasdfk"]);
        // return response()->json(session("refresh_token"), 200);

        /**
         * test get
         */

        // $data = Sto::getsSto();

        // return response()->json($data, 200);
    }

    public function getSto()
    {
        $data = Sto::getsSto();
        return response()->json($data, 200);
    }

    public function logout()
    {
        $request = Sto::logout();
        return response()->json($request, 200);
    }


    public function generate_approval_cuti()
    {
        $cutis = Cutie::all();
        $datas = [];
        DB::beginTransaction();
        try {
            foreach ($cutis as $data) {
                $posisi = $data->karyawan->posisi;
                $my_jabatan = $data->karyawan->posisi[0]->jabatan_id;
                $list_atasan = Approval::ListAtasan($posisi);
                $has_leader = $list_atasan['leader'] ?? null;
                $has_section_head = $list_atasan['section_head'] ?? null;
                $has_department_head = $list_atasan['department_head'] ?? null;
                $has_division_head = $list_atasan['division_head'] ?? null;
                $has_director = $list_atasan['director'] ?? null;

                $approved_for = null;
                $approved = $data->approved_by ? Karyawan::where('nama', $data->approved_by)->first() : null;
                $approved_by = $approved ? $approved->posisi[0]->id_posisi : null;
                $approved_karyawan_id = $approved ? $approved->id_karyawan : null;

                $checked2_for = null;
                $checked2 = $data->checked2_by ? Karyawan::where('nama', $data->checked2_by)->first() : null;
                $checked2_by = $checked2 ? $checked2->posisi[0]->id_posisi : ($approved ? $approved->posisi[0]->id_posisi : null);
                $checked2_karyawan_id = $checked2 ? $checked2->id_karyawan : ($approved ? $approved->id_karyawan : null);

                $checked1_for = null;
                $checked1 = $data->checked1_by  ? Karyawan::where('nama', $data->checked1_by)->first() : null;
                $checked1_by = $checked1 ? $checked1->posisi[0]->id_posisi : ($checked2 ? $checked2->posisi[0]->id_posisi : ($approved ? $approved->posisi[0]->id_posisi : null));
                $checked1_karyawan_id = $checked1 ? $checked1->id_karyawan : ($checked2 ? $checked2->id_karyawan : ($approved ? $approved->id_karyawan : null));

                //KONDISI 1 (PUNYA SEMUA)
                if ($has_leader && $has_section_head && $has_department_head) {
                    $checked1_for = $has_leader;
                    $checked2_for = $has_section_head;
                    $approved_for = $has_department_head;
                }

                //KONDISI 2 (HANYA PUNYA LEADER & SECTION HEAD)
                if ($has_leader && $has_section_head && !$has_department_head) {
                    $checked1_for = $has_leader;
                    $checked2_for = $has_section_head;
                    $approved_for = $has_section_head;
                }

                //KONDISI 3 (HANYA PUNYA LEADER DAN DEPARTMENT HEAD)
                if ($has_leader && !$has_section_head && $has_department_head) {
                    $checked1_for = $has_leader;
                    $checked2_for = $has_department_head;
                    $approved_for = $has_department_head;
                }

                //KONDISI 4 (HANYA PUNYA DEPARTMENT HEAD)
                if (!$has_leader && !$has_section_head && $has_department_head) {
                    $checked1_for = $has_department_head;
                    $checked2_for = $has_department_head;
                    $approved_for = $has_department_head;
                }

                //KONDISI 5 (HANYA PUNYA SECTION HEAD)
                if (!$has_leader && $has_section_head && !$has_department_head) {
                    $checked1_for = $has_section_head;
                    $checked2_for = $has_section_head;
                    $approved_for = $has_section_head;
                }

                //KONDISI 6 (HANYA PUNYA SECTION HEAD DAN DEPARTMENT HEAD)
                if (!$has_leader && $has_section_head && $has_department_head) {
                    $checked1_for = $has_section_head;
                    $checked2_for = $has_section_head;
                    $approved_for = $has_department_head;
                }

                //KONDISI 7 (HANYA PUNYA DIVISION HEAD)
                if (!$has_leader && !$has_section_head && !$has_department_head) {
                    $checked1_for = $has_division_head;
                    $checked2_for = $has_division_head;
                    $approved_for = $has_division_head;
                }

                //KONDISI 8 (HANYA PUNYA DIRECTOR)
                if (!$has_leader && !$has_section_head && !$has_department_head && $my_jabatan == 2) {
                    $checked1_for = $has_director;
                    $checked2_for = $has_director;
                    $approved_for = $has_director;
                }

                $approval = ApprovalCuti::create([
                    'cuti_id' => $data->id_cuti,
                    'checked1_for' => $checked1_for,
                    'checked1_by' => $checked1_by,
                    'checked1_karyawan_id' => $checked1_karyawan_id,
                    'checked2_for' => $checked2_for,
                    'checked2_by' => $checked2_by,
                    'checked2_karyawan_id' => $checked2_karyawan_id,
                    'approved_for' => $approved_for,
                    'approved_by' => $approved_by,
                    'approved_karyawan_id' => $approved_karyawan_id,
                ]);

                $datas[] = $approval;
            }
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }

        return response()->json($datas, 200);
    }

    public function upload_pin_view()
    {
        $dataPage = [
            'pageTitle' => "Test Upload Pin",
            'page' => 'test-upload-pin',
        ];
        return view('test', $dataPage);
    }

    public function upload_pin(Request $request)
    {
        $file = $request->file('file_pin');
        $organisasi_id = auth()->user()->organisasi_id;
        
        $validator = Validator::make($request->all(), [
            'file_pin' => 'required|mimes:xlsx,xls'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'File Harus bertipe Excel!'], 400);
        }

        DB::beginTransaction();
        try {

            if($request->hasFile('file_pin')){
                $records = 'PIN_' . time() . '.' . $file->getClientOriginalExtension();
                $pin_file = $file->storeAs("attachment/upload-pin-karyawan", $records);
            } 

            if (file_exists(storage_path("app/public/".$pin_file))) {
                $spreadsheet = IOFactory::load(storage_path("app/public/".$pin_file));
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();
                unset($data[0]);
                if(!empty($data)){
                    foreach ($data as $key => $row) {
                        $karyawan = Karyawan::where('ni_karyawan', $row[0])->organisasi($organisasi_id)->first();
                        if($karyawan){
                            $karyawan->update([
                                'pin' => $row[2]
                            ]);
                        } else {
                            continue;
                        }
                    }
                } else {
                    DB::rollback();
                    return response()->json(['message' => 'File Kosong'], 400);
                }
                DB::commit();
                return response()->json(['message' => 'Sukses'], 200);
            } else {
                DB::rollback();
                return response()->json(['message' => 'Gagal!'], 404);
            }
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error processing the file: ' . $e->getMessage()], 500);
        }
    }

    public function test_rekap_presensi()
    {
        $dataFilter = [];
        $dataFilter['organisasi_id'] = 2;
        $dataFilter['start'] = '2025-01-20';
        $dataFilter['end'] = '2025-01-30';
        $data = ScanlogDetail::rekapKehadiran($dataFilter);
        return response()->json($data, 200);
    }

    public function send_whatsapp_message()
    {
        $client = new Client();

        try {
            $response = $client->post('https://api.fonnte.com/send', [
                'form_params' => [ 
                    'target' => '120363375659726514@g.us,085871262080',
                    'message' => 'TEST NOTIF WA - HRIS KE 2',
                ],
                'headers' => [
                    'Authorization' => env('API_TOKEN_FONNTE'),
                ],
            ]);

            $responseData = json_decode($response->getBody(), true);

            return response()->json($responseData, $response->getStatusCode());

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to send message'], 500); 
        }
    }

    public function fetch_whatsapp_group()
    {
        $client = new Client();

        try {
            $response = $client->post('https://api.fonnte.com/fetch-group', [
                'headers' => [
                    'Authorization' => env('API_TOKEN_FONNTE'),
                ],
            ]);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $responseData = json_decode($response->getBody(), true);
            return response()->json($responseData, $response->getStatusCode());
            } else {
            return response()->json(['error' => 'Failed to fetch group'], $response->getStatusCode()); // Return the error status code
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch group'], 500); // Internal Server Error
        }
    }

    public function start_whatsapp_client()
    {
        $client = new Client();
        $url = env('API_URL_WHATSAPP').env('CLIENT_ID_TCF2').'/start-client';
        
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'x-api-key' => env('API_KEY_WHATSAPP'),
        ];

        $response = $client->get($url, [
            'headers' => $headers,
        ]);

        return response()->json(json_decode($response->getBody(), true), $response->getStatusCode());
    }

    public function add_whatsapp_user()
    {
        $client = new Client();
        $url = env('API_URL_WHATSAPP').'add-user';
        $organisasi_id = auth()->user()->organisasi_id;
        $body = [
            'email' => 'personalia2@tcf.com',
            'password' => 'nevergiveup',
            'name' => 'NOTIFICATION BOT TCF2',
        ];
        
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'x-api-key' => env('API_KEY_WHATSAPP'),
        ];

        $response = $client->post($url, [
            'headers' => $headers,
            'json' => $body,
        ]);

        return response()->json($response->getBody(), $response->getStatusCode());
    }

    public function add_whatsapp_device()
    {
        $client = new Client();
        $url = env('API_URL_WHATSAPP').'add-device';
        $organisasi_id = auth()->user()->organisasi_id;
        $body = [
            'user_id' => $organisasi_id == 1 ? env('CLIENT_ID_TCF2') : env('CLIENT_ID_TCF3'),
            'name' => 'NOTIFICATION BOT TCF2',
            'phone_number' => '087887736910',
        ];
        
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'x-api-key' => env('API_KEY_WHATSAPP'),
        ];

        $response = $client->post($url, [
            'headers' => $headers,
            'json' => $body,
        ]);

        return response()->json($response->getBody(), $response->getStatusCode());
    }

    public function get_whatsapp_group()
    {
        $client = new Client();

        try {
            $response = $client->post('https://api.fonnte.com/get-whatsapp-group', [
                'headers' => [
                    'Authorization' => env('API_TOKEN_FONNTE')
                ],
            ]);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $responseData = json_decode($response->getBody(), true);
            return response()->json($responseData, $response->getStatusCode());
            } else {
            return response()->json(['error' => 'Failed to get WhatsApp group'], $response->getStatusCode());
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get WhatsApp group'], 500);
        }
    }

    public function send_whatsapp_message_v2()
    {
        $organisasi_id = auth()->user()->organisasi_id;
        $message = 'Test API from LARAVEL 14.50';
        $phone_number = '628987335266@c.us';
        $result = SendWhatsappNotification::send($message, $organisasi_id, $phone_number);
        dd($result);
    }
}
