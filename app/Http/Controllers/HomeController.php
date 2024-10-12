<?php

namespace App\Http\Controllers;

use App\Models\Cutie;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $profile = auth()->user()?->karyawan;
        $data = [];
        if($profile){
            $data = [
                'ni_karyawan' => $profile->ni_karyawan,
                'nama' => $profile->nama,
                'no_kk' => $profile->no_kk,
                'nik' => $profile->nik,
                'tempat_lahir' => $profile->tempat_lahir,
                'tanggal_lahir' => $profile->tanggal_lahir,
                'jenis_kelamin' => $profile->jenis_kelamin,
                'agama' => $profile->agama,
                'gol_darah' => $profile->gol_darah,
                'status_keluarga' => $profile->status_keluarga,
                'kategori_keluarga' => $profile->kategori_keluarga,
                'alamat' => $profile->alamat,
                'domisili' => $profile->domisili,
                'no_telp' => $profile->no_telp,
                'no_telp_darurat' => $profile->no_telp_darurat,
                'email' => $profile->email,
                'npwp' => $profile->npwp,
                'no_bpjs_ks' => $profile->no_bpjs_ks,
                'no_bpjs_kt' => $profile->no_bpjs_kt,
                'no_rekening' => $profile->no_rekening,
                'nama_rekening' => $profile->nama_rekening,
                'nama_bank' => $profile->nama_bank,
                'nama_ibu_kandung' => $profile->nama_ibu_kandung,
                'jenjang_pendidikan' => $profile->jenjang_pendidikan,
                'jurusan_pendidikan' => $profile->jurusan_pendidikan,
                'jenis_kontrak' => $profile->jenis_kontrak,
                'status_karyawan' => $profile->status_karyawan,
                'sisa_cuti_pribadi' => $profile->sisa_cuti_pribadi,
                'sisa_cuti_bersama' => $profile->sisa_cuti_bersama,
                'hutang_cuti' => $profile->hutang_cuti,
                'tanggal_mulai' => $profile->tanggal_mulai,
                'tanggal_selesai' => $profile->tanggal_selesai,
                'posisi' => $profile?->posisi()?->pluck('posisis.nama'),
                'grup' => $profile->grup->nama,
            ];
        } else {
            $data = [
                'ni_karyawan' => null,
                'nama' => null,
                'no_kk' => null,
                'nik' => null,
                'tempat_lahir' => null,
                'tanggal_lahir' => null,
                'jenis_kelamin' => null,
                'agama' => null,
                'gol_darah' => null,
                'status_keluarga' => null,
                'kategori_keluarga' => null,
                'alamat' => null,
                'domisili' => null,
                'no_telp' => null,
                'no_telp_darurat' => null,
                'email' => null,
                'npwp' => null,
                'no_bpjs_ks' => null,
                'no_bpjs_kt' => null,
                'no_rekening' => null,
                'nama_rekening' => null,
                'nama_bank' => null,
                'nama_ibu_kandung' => null,
                'jenjang_pendidikan' => null,
                'jurusan_pendidikan' => null,
                'jenis_kontrak' => null,
                'status_karyawan' => null,
                'sisa_cuti_pribadi' => null,
                'sisa_cuti_bersama' => null,
                'hutang_cuti' => null,
                'tanggal_mulai' => null,
                'tanggal_selesai' => null,
                'posisi' => null,
                'grup' => null,
            ];
        }

        $dataPage = [
            'pageTitle' => "SuperApps - Menu",
            'page' => 'menu',
            'profile' => $data
        ];
        return view('pages.menu.index', $dataPage);
    }

    public function get_notification(){
        $notification = [];
        $today = date('Y-m-d');
        $user = auth()->user();
        $tenggang_karyawans = [];

        if($user->hasRole('personalia') || $user->hasRole('super user')){
            $tenggang_karyawans = Karyawan::where('status_karyawan', 'AT')
                ->whereRaw('(tanggal_selesai - ?) <= 30', [$today])
                ->selectRaw('*, (tanggal_selesai - ?) as jumlah_hari', [$today])
                ->get();

            $cutie_approval = Cutie::selectRaw('cutis.*, karyawans.nama, (rencana_mulai_cuti - ?) as jumlah_hari',[$today])->leftJoin('karyawans', 'cutis.karyawan_id', 'karyawans.id_karyawan')
                ->where('status_dokumen', 'WAITING')
                ->whereNotNull('approved_by')
                ->whereNull('legalized_by')
                ->get();

            $rejected_cuti = [];

        } elseif ($user->hasRole('atasan')){
            $me = auth()->user()->karyawan;
            $posisi = $user->karyawan->posisi;
            $id_posisi_members = $this->get_member_posisi($posisi);

            foreach ($posisi as $ps){
                $index = array_search($ps->id_posisi, $id_posisi_members);
                array_splice($id_posisi_members, $index, 1);
            }

            $members = $id_posisi_members;

            $tenggang_karyawans = Karyawan::where('status_karyawan', 'AT')
                ->whereRaw('(tanggal_selesai - ?) <= 30', [$today])
                ->whereHas('posisi', function($query) use ($members) {
                    $query->whereIn('posisi_id', $members);
                })
                ->selectRaw('*, (tanggal_selesai - ?) as jumlah_hari', [$today])
                ->get();
            
            // Notif Approval
            $cutie_approval = Cutie::selectRaw('cutis.*, karyawans.nama, (rencana_mulai_cuti - ?) as jumlah_hari',[$today])->leftJoin('karyawans', 'cutis.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoin('karyawan_posisi', 'cutis.karyawan_id', 'karyawan_posisi.karyawan_id')
            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
            ->where('status_dokumen', 'WAITING')
            ->where(function($query) {
                $query->orWhereNull('approved_by')
                        ->orWhereNull('checked1_by')
                        ->orWhereNull('checked2_by');
                })
            ->whereIn('posisis.id_posisi', $members)
            ->whereRaw('(rencana_mulai_cuti - ?) <= 7', [$today])
            ->get();


            $rejected_cuti = Cutie::selectRaw('cutis.*, karyawans.nama')->leftJoin('karyawans', 'cutis.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoin('karyawan_posisi', 'cutis.karyawan_id', 'karyawan_posisi.karyawan_id')
            ->where('status_dokumen', 'REJECTED')
            ->where('cutis.karyawan_id', $me->id_karyawan)
            ->whereRaw('DATE(rejected_at) <= (rencana_mulai_cuti + INTERVAL \'3 days\')')
            ->get()->toArray();

        } else {
            $me = auth()->user()->karyawan;
            $cutie_approval = Cutie::selectRaw('cutis.*, karyawans.nama, (rencana_mulai_cuti - ?) as jumlah_hari',[$today])->leftJoin('karyawans', 'cutis.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoin('karyawan_posisi', 'cutis.karyawan_id', 'karyawan_posisi.karyawan_id')
            ->where('status_dokumen', 'WAITING')
            ->where('cutis.karyawan_id', $me->id_karyawan)
            ->whereRaw('(rencana_mulai_cuti - ?) <= 7', [$today])
            ->get();

            $rejected_cuti = Cutie::selectRaw('cutis.*, karyawans.nama')->leftJoin('karyawans', 'cutis.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoin('karyawan_posisi', 'cutis.karyawan_id', 'karyawan_posisi.karyawan_id')
            ->where('status_dokumen', 'REJECTED')
            ->where('cutis.karyawan_id', $me->id_karyawan)
            ->whereRaw('DATE(rejected_at) <= (rencana_mulai_cuti + INTERVAL \'3 days\')')
            ->get()->toArray();

            $tenggang_karyawans = Karyawan::where('status_karyawan', 'AT')->where('id_karyawan', $user->karyawan->id_karyawan)
                ->whereRaw('(tanggal_selesai - ?) <= 30', [$today])
                ->selectRaw('*, (tanggal_selesai - ?) as jumlah_hari', [$today])
                ->get();
        }

        $notification = [
            'count_notif' => $tenggang_karyawans?->count() + $cutie_approval?->count() + count($rejected_cuti),
            'list' => $tenggang_karyawans->toArray(),
            'cutie_approval' => $cutie_approval->toArray(),
            'rejected_cuti' => $rejected_cuti
        ];

        $html = view('layouts.partials.notification')->with(compact('notification'))->render();
        return response()->json(['data' => $html], 200);
    }

    function get_member_posisi($posisis)
    {
        $data = [];
        foreach ($posisis as $ps) {
            if ($ps->children) {
                $data = array_merge($data, $this->get_member_posisi($ps->children));
            }
            $data[] = $ps->id_posisi;
        }
        return $data;
    }
}
