<?php

namespace App\Http\Controllers\Cutie;

use Throwable;
use Carbon\Carbon;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Services\CutiService;
use App\Services\KaryawanService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BypassController extends Controller
{

    private $cutiService, $karyawanService;
    public function __construct(
        CutiService $cutiService,
        KaryawanService $karyawanService
    )
    {
        $this->cutiService = $cutiService;
        $this->karyawanService = $karyawanService;
    }

    public function index()
    {
        $dataPage = [
            'pageTitle' => "Cuti-E - Bypass Cuti",
            'page' => 'cutie-bypass-cuti',
        ];

        return view('pages.cuti-e.bypass-cuti', $dataPage);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_karyawan' => 'required',
            'penggunaan_sisa_cuti' => 'required|in:TL,TB',
            'rencana_mulai_cuti' => 'date|required',
            'rencana_selesai_cuti' => 'date|required|after_or_equal:rencana_mulai_cuti',
            'alasan_cuti' => 'required',
            'durasi_cuti' => 'numeric|required',
        ]);

        $organisasi_id = auth()->user()->organisasi_id;
        $id_karyawan = $request->id_karyawan;
        $karyawan = Karyawan::find($id_karyawan);
        $posisi = $karyawan->posisi;
        $sisa_cuti_pribadi = $karyawan->sisa_cuti_pribadi;
        $sisa_cuti_tahun_lalu = $karyawan->sisa_cuti_tahun_lalu;
        $expired_date_cuti_tahun_lalu = $karyawan->expired_date_cuti_tahun_lalu;

        $penggunaan_sisa_cuti = $request->penggunaan_sisa_cuti;
        $rencana_mulai_cuti = $request->rencana_mulai_cuti;
        $rencana_selesai_cuti = $request->rencana_selesai_cuti;
        $alasan_cuti = $request->alasan_cuti;
        $durasi_cuti = $request->durasi_cuti;

        DB::beginTransaction();
        try{
            if($penggunaan_sisa_cuti == 'TB'){
                $jatah_cuti = $sisa_cuti_pribadi - $durasi_cuti;
                if($sisa_cuti_pribadi < 0){
                    DB::rollback();
                    return response()->json(['message' => 'Sisa cuti pribadi karyawan tidak mencukupi, Hubungi HRD untuk informasi lebih lanjut!'], 402);
                } else {
                    if($jatah_cuti < 0){
                        DB::rollback();
                        return response()->json(['message' => 'Sisa cuti pribadi karyawan tidak mencukupi, Silahkan baca ketentuan pembagian cuti pribadi lagi!'], 402);
                    } else {
                        $dataKaryawan = [
                            'sisa_cuti_pribadi' => $jatah_cuti
                        ];
                        $karyawan = $this->karyawanService->updateKaryawan($id_karyawan, $dataKaryawan);
                    }
                }
            } else {
                $jatah_cuti = $sisa_cuti_tahun_lalu - $durasi_cuti;
                if($sisa_cuti_tahun_lalu < 0){
                    DB::rollback();
                    return response()->json(['message' => 'Karyawan tidak memiliki sisa cuti tahun lalu!, Silahkan input menggunakan sisa cuti tahun berjalan karyawan!'], 402);
                } else {
                    if($jatah_cuti < 0){
                        DB::rollback();
                        return response()->json(['message' => 'Sisa cuti tahun lalu karyawan tidak mencukupi, Silahkan input menggunakan sisa cuti tahun berjalan karyawan!!'], 402);
                    } else {
                        $dataKaryawan = [
                            'sisa_cuti_tahun_lalu' => $jatah_cuti
                        ];
                        $karyawan = $this->karyawanService->updateKaryawan($id_karyawan, $dataKaryawan);
                    }
                }
            }

            $today = Carbon::now();
            if ($today->between(Carbon::parse($rencana_mulai_cuti), Carbon::parse($rencana_selesai_cuti))) {
                $status_cuti = 'ON LEAVE';
                $aktual_mulai_cuti = $rencana_mulai_cuti;
            } elseif (Carbon::parse($rencana_selesai_cuti)->lt($today)) {
                $status_cuti = 'COMPLETED';
                $aktual_mulai_cuti = $rencana_mulai_cuti;
                $aktual_selesai_cuti = $rencana_selesai_cuti;
            } else {
                $status_cuti = 'SCHEDULED';
                $aktual_mulai_cuti = null;
                $aktual_selesai_cuti = null;
            }

            $dataCuti = [
                'karyawan_id' => $id_karyawan,
                'organisasi_id' => $karyawan->user->organisasi_id,
                'jenis_cuti' => 'PRIBADI',
                'attachment' => null,
                'rencana_mulai_cuti' => $rencana_mulai_cuti,
                'rencana_selesai_cuti' => $rencana_selesai_cuti,
                'alasan_cuti' => $alasan_cuti,
                'durasi_cuti' => $durasi_cuti,
            ];
            $cuti = $this->cutiService->createCuti($dataCuti);
            $approvalCuti = $this->cutiService->getStructureApprovalCuti($posisi);
            if (!empty($approvalCuti)) {
                $dataApprovalCuti = [
                    'cuti_id' => $cuti->id_cuti,
                    'checked1_for' => $approvalCuti['checked1_for'],
                    'checked2_for' => $approvalCuti['checked2_for'],
                    'approved_for' => $approvalCuti['approved_for'],
                ];
                $this->cutiService->createApprovalCuti($dataApprovalCuti);
            } else {
                DB::rollback();
                return response()->json(['message' => 'Approval cuti tidak ditemukan, hubungi HRD untuk informasi lebih lanjut!'], 402);
            }

            // $message = "Nama : *" . $karyawan->nama . "*\n" .
            //         "Jenis Cuti : PRIBADI (BYPASS) \n" .
            //         "Pembuat dokumen tetap harus melakukan approval \nSegera lakukan approval pada sistem.\n" .
            //         "Klik link dibawah untuk melakukan approval \n" .
            //         ($organisasi_id == 1 ? env('URL_SERVER_HRIS_TCF2') : env('URL_SERVER_HRIS_TCF3'))."cutie/member-cuti";
            // $this->send_whatsapp($id_karyawan, $approval_cuti?->checked1_for, $message, $organisasi_id);
            DB::commit();
            return response()->json(['message' => 'Bypass Cuti Berhasil Dilakukan!'], 200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }
}
