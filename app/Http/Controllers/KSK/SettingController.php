<?php

namespace App\Http\Controllers\KSK;

use Exception;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\KSK\CleareanceSetting;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function index()
    {
        $deptIT = CleareanceSetting::where('organisasi_id', auth()->user()->organisasi_id)->where('type', 'IT')->first();
        $deptGA = CleareanceSetting::where('organisasi_id', auth()->user()->organisasi_id)->where('type', 'GA')->first();
        $deptHR = CleareanceSetting::where('organisasi_id', auth()->user()->organisasi_id)->where('type', 'HR')->first();
        $deptFAT = CleareanceSetting::where('organisasi_id', auth()->user()->organisasi_id)->where('type', 'FAT')->first();

        $dataPage = [
            'pageTitle' => "KSK-E - Setting",
            'page' => 'ksk-setting',
            'deptIT' => $deptIT,
            'deptGA' => $deptGA,
            'deptHR' => $deptHR,
            'deptFAT' => $deptFAT,
        ];
        return view('pages.ksk-e.setting.index', $dataPage);
    }

    public function update(Request $request)
    {
        $dataValidate = [
            'dept_it' => ['required', 'exists:karyawans,id_karyawan'],
            'dept_fat' => ['required', 'exists:karyawans,id_karyawan'],
            'dept_ga' => ['required', 'exists:karyawans,id_karyawan'],
            'dept_hr' => ['required', 'exists:karyawans,id_karyawan'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        DB::beginTransaction();
        try {
            $settings = CleareanceSetting::where('organisasi_id', auth()->user()->organisasi_id)->get();
            if ($settings->isNotEmpty()) {
                foreach ($settings as $setting) {
                    if ($setting->type == 'IT') {
                        $setting->karyawan_id = $request->dept_it;
                        $setting->ni_karyawan = Karyawan::find($request->dept_it)->ni_karyawan;
                        $setting->nama_karyawan = Karyawan::find($request->dept_it)->nama;
                        $setting->save();
                    } elseif ($setting->type == 'GA') {
                        $setting->karyawan_id = $request->dept_ga;
                        $setting->ni_karyawan = Karyawan::find($request->dept_ga)->ni_karyawan;
                        $setting->nama_karyawan = Karyawan::find($request->dept_ga)->nama;
                        $setting->save();
                    } elseif ($setting->type == 'HR') {
                        $setting->karyawan_id = $request->dept_hr;
                        $setting->ni_karyawan = Karyawan::find($request->dept_hr)->ni_karyawan;
                        $setting->nama_karyawan = Karyawan::find($request->dept_hr)->nama;
                        $setting->save();
                    } elseif ($setting->type == 'FAT') {
                        $setting->karyawan_id = $request->dept_fat;
                        $setting->ni_karyawan = Karyawan::find($request->dept_fat)->ni_karyawan;
                        $setting->nama_karyawan = Karyawan::find($request->dept_fat)->nama;
                        $setting->save();
                    }
                }
            } else {
                CleareanceSetting::create([
                    'organisasi_id' => auth()->user()->organisasi_id,
                    'karyawan_id' => $request->dept_it,
                    'ni_karyawan' => Karyawan::find($request->dept_it)->ni_karyawan,
                    'nama_karyawan' => Karyawan::find($request->dept_it)->nama,
                    'type' => 'IT',
                ]);

                CleareanceSetting::create([
                    'organisasi_id' => auth()->user()->organisasi_id,
                    'karyawan_id' => $request->dept_ga,
                    'ni_karyawan' => Karyawan::find($request->dept_ga)->ni_karyawan,
                    'nama_karyawan' => Karyawan::find($request->dept_ga)->nama,
                    'type' => 'GA',
                ]);

                CleareanceSetting::create([
                    'organisasi_id' => auth()->user()->organisasi_id,
                    'karyawan_id' => $request->dept_hr,
                    'ni_karyawan' => Karyawan::find($request->dept_hr)->ni_karyawan,
                    'nama_karyawan' => Karyawan::find($request->dept_hr)->nama,
                    'type' => 'HR',
                ]);

                CleareanceSetting::create([
                    'organisasi_id' => auth()->user()->organisasi_id,
                    'karyawan_id' => $request->dept_fat,
                    'ni_karyawan' => Karyawan::find($request->dept_fat)->ni_karyawan,
                    'nama_karyawan' => Karyawan::find($request->dept_fat)->nama,
                    'type' => 'FAT',
                ]);
            }
            DB::commit();
            return response()->json(['message' => 'Data setting berhasil disimpan'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
