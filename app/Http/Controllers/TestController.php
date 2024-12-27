<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Cutie;
use App\Models\Karyawan;
use App\Helpers\Approval;
use App\Helpers\Sto;
use App\Models\ApprovalCuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function index()
    {
        $getCustomers = DB::connection('idempiere')->table('c_bpartner')
            ->select('c_bpartner_id', 'name')
            ->where('c_bpartner_id', '1000010')->first();

        $getProducts = DB::connection('idempiere')->table('m_product')
            ->leftJoin('c_bpartner_product', 'm_product.m_product_id', '=', 'c_bpartner_product.m_product_id')
            ->select('m_product.m_product_id', 'm_product.value', 'm_product.name', 'm_product.description', 'classification')
            ->where([
                ['m_product.isactive', '=', "Y"],
                ['c_bpartner_product.c_bpartner_id', $getCustomers->c_bpartner_id]
            ])->get();

        $product = $getProducts->random(1);

        return response()->json($product[0], 200);

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
}
