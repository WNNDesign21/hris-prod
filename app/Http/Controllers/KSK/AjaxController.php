<?php

namespace App\Http\Controllers\KSK;

use Throwable;
use Carbon\Carbon;
use App\Models\Posisi;
use App\Models\KSK\KSK;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Models\KSK\DetailKSK;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AjaxController extends Controller
{
    public function select_get_posisis(Request $request){
        $search = $request->input('search');
        $page = $request->input("page");
        $idCats = $request->input('catsProd');
        $adOrg = $request->input('adOrg');

        $query = Posisi::select(
            'posisis.id_posisi',
            'posisis.nama',
        );

        if (!empty($search)) {
            $query->where(function ($dat) use ($search) {
                $dat->where('karyawans.id_karyawan', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%");
            });
        }

        $data = $query->simplePaginate(10);

        $morePages = true;
        $pagination_obj = json_encode($data);
        if (empty($data->nextPageUrl())) {
            $morePages = false;
        }

        foreach ($data->items() as $posisis) {
            $dataUser[] = [
                'id' => $posisis->id_posisi,
                'text' => $posisis->nama
            ];
        }

        $results = array(
            "results" => $dataUser,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }

    public function get_karyawans(Request $request)
    {
        $dataValidate = [
            'id_departemen' => ['nullable','exists:departemens,id_departemen'],
            'id_divisi' => ['nullable', 'exists:divisis,id_divisi'],
            'tahun_selesai' => ['required', 'numeric'],
            'bulan_selesai' => ['required', 'numeric'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $id_departemen = $request->id_departemen;
        $id_divisi = $request->id_divisi;
        $parent_id = $request->parent_id;
        $tahun_selesai = $request->tahun_selesai;
        $bulan_selesai = $request->bulan_selesai;

        try {
            $dataFilter = [
                'id_departemen' => $id_departemen,
                'id_divisi' => $id_divisi,
                'parent_id' => $parent_id,
                'tahun_selesai' => $tahun_selesai,
                'bulan_selesai' => $bulan_selesai,
            ];

            $datas = Karyawan::getKaryawanKsk($dataFilter);
            $html = view('layouts.partials.ksk-list-karyawan-release', ['datas' => $datas])->render();
            return response()->json(['message' => 'success', 'data' => $datas, 'html' => $html], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_detail_ksk_release(string $id)
    {
        try {
            // $detail_ksk = DetailKSK::with(['kontrak' => function ($query) {
            //     $query->orderBy('tanggal_selesai', 'ASC');
            // }])->select('ksk_details.*', 'karyawans.tanggal_mulai', 'karyawans.tanggal_selesai', 'ksk.*', 'kontraks.tanggal_mulai as latest_kontrak_tanggal_mulai', 'kontraks.tanggal_selesai as latest_kontrak_tanggal_selesai')->where('ksk_id', $id)->leftJoin('karyawans', 'ksk_details.karyawan_id', 'karyawans.id_karyawan')->leftJoin('ksk', 'ksk_details.ksk_id', 'ksk.id_ksk')
            // ->leftJoin('kontraks', function ($join) {
            //     $join->on('karyawans.id_karyawan', '=', 'kontraks.karyawan_id')
            //         ->whereRaw('kontraks.tanggal_selesai = (select max(tanggal_selesai) from kontraks where kontraks.karyawan_id = karyawans.id_karyawan)');
            // })
            // ->get();
            $detail_ksk = DetailKSK::where('ksk_id', $id)->get();
            $html = view('layouts.partials.ksk-list-karyawan-release-detail', ['datas' => $detail_ksk])->render();
            return response()->json(['message' => 'success', 'data' => $detail_ksk, 'html' => $html], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_detail_ksk_tindak_lanjut(string $id)
    {
        try {
            $detail_ksk = DetailKSK::with(['ksk', 'changeHistoryKSK'])->find($id);
            $html = view('layouts.partials.ksk.modal-body-tindak-lanjut', ['detail_ksk' => $detail_ksk])->render();
            return response()->json(['message' => 'success', 'data' => $detail_ksk, 'html' => $html], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_ksk(string $id)
    {
        try {
            $ksk = KSK::find($id);
            $data = [];
            if (!empty($ksk)) {
                $releasedFormatted = $ksk->released_by ? '✅'.$ksk->released_by.'<br>'.Carbon::createFromFormat('Y-m-d H:i:s', $ksk->released_at)->format('d F Y H:i') : '⏳ Waiting';
                $checkedFormatted = $ksk->checked_by ? '✅'.$ksk->checked_by.'<br>'.Carbon::createFromFormat('Y-m-d H:i:s', $ksk->checked_at)->format('d F Y H:i') : '⏳ Waiting';
                $approvedFormatted = $ksk->approved_by ? '✅'.$ksk->approved_by.'<br>'.Carbon::createFromFormat('Y-m-d H:i:s', $ksk->approved_at)->format('d F Y H:i') : '⏳ Waiting';
                $reviewedDivFormatted = $ksk->reviewed_div_by ? '✅'.$ksk->reviewed_div_by.'<br>'.Carbon::createFromFormat('Y-m-d H:i:s', $ksk->reviewed_div_at)->format('d F Y H:i') : '⏳ Waiting';
                $reviewedPhFormatted = $ksk->reviewed_ph_by ? '✅'.$ksk->reviewed_ph_by.'<br>'.Carbon::createFromFormat('Y-m-d H:i:s', $ksk->reviewed_ph_at)->format('d F Y H:i') : '⏳ Waiting';
                $reviewedDirFormatted = $ksk->reviewed_dir_by ? '✅'.$ksk->reviewed_dir_by.'<br>'.Carbon::createFromFormat('Y-m-d H:i:s', $ksk->reviewed_dir_at)->format('d F Y H:i') : '⏳ Waiting';
                $legalizedFormatted = $ksk->legalized_by ? '✅'.$ksk->legalized_by.'<br>'.Carbon::createFromFormat('Y-m-d H:i:s', $ksk->legalized_at)->format('d F Y H:i') : '⏳ Waiting';

                $data = [
                    'released_by' => $releasedFormatted,
                    'checked_by'=> $checkedFormatted,
                    'approved_by'=> $approvedFormatted,
                    'reviewed_div_by'=> $reviewedDivFormatted,
                    'reviewed_ph_by'=> $reviewedPhFormatted,
                    'reviewed_dir_by'=> $reviewedDirFormatted,
                    'legalized_by'=> $legalizedFormatted,
                ];
            }
            return response()->json(['message' => 'success', 'data' => $data], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_approval_ksk(string $id)
    {
        try {
            $ksk = KSK::with([
                'detailKSK' => function ($query) {
                    $query->with([
                        'kontrak' => function ($query) {
                            $query->orderBy('tanggal_selesai', 'ASC');
                        },
                        'changeHistoryKSK' => function ($query) {
                            $query->get();
                        }
                    ])->select('ksk_details.*', 'karyawans.tanggal_mulai', 'karyawans.tanggal_selesai', 'ksk.*', 'kontraks.tanggal_mulai as latest_kontrak_tanggal_mulai', 'kontraks.tanggal_selesai as latest_kontrak_tanggal_selesai')->leftJoin('karyawans', 'ksk_details.karyawan_id', 'karyawans.id_karyawan')->leftJoin('ksk', 'ksk_details.ksk_id', 'ksk.id_ksk')
                    ->leftJoin('kontraks', function ($join) {
                        $join->on('karyawans.id_karyawan', '=', 'kontraks.karyawan_id')
                            ->whereRaw('kontraks.tanggal_selesai = (select max(tanggal_selesai) from kontraks where kontraks.karyawan_id = karyawans.id_karyawan)');
                    });
                }
            ])->find($id);
            $html = view('layouts.partials.ksk.modal-body-approval', ['ksk' => $ksk])->render();
            return response()->json(['message' => 'success', 'html' => $html], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_detail_ksk_approval(string $id)
    {
        try {
            // $ksk = KSK::with([
            //     'detailKSK' => function ($query) {
            //         $query->with([
            //             'kontrak' => function ($query) {
            //                 $query->orderBy('tanggal_selesai', 'ASC');
            //             },
            //             'changeHistoryKSK' => function ($query) {
            //                 $query->get();
            //             }
            //         ])->select('ksk_details.*', 'karyawans.tanggal_mulai', 'karyawans.tanggal_selesai', 'ksk.*', 'kontraks.tanggal_mulai as latest_kontrak_tanggal_mulai', 'kontraks.tanggal_selesai as latest_kontrak_tanggal_selesai')->leftJoin('karyawans', 'ksk_details.karyawan_id', 'karyawans.id_karyawan')->leftJoin('ksk', 'ksk_details.ksk_id', 'ksk.id_ksk')
            //         ->leftJoin('kontraks', function ($join) {
            //             $join->on('karyawans.id_karyawan', '=', 'kontraks.karyawan_id')
            //                 ->whereRaw('kontraks.tanggal_selesai = (select max(tanggal_selesai) from kontraks where kontraks.karyawan_id = karyawans.id_karyawan)');
            //         });
            //     }
            // ])->find($id);
            $ksk = KSK::find($id);
            $html = view('layouts.partials.ksk.modal-body-detail-approval', ['ksk' => $ksk])->render();
            return response()->json(['message' => 'success', 'html' => $html], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
