<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Cutie;
use App\Models\Posisi;
use App\Models\Karyawan;
use App\Models\DetailLembur;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $notification = [];
        $today = date('Y-m-d');
        $user = auth()->user();
        $tenggang_karyawans = [];

        if($user->hasRole('personalia') || $user->hasRole('super user')){
            $my_cutie = null;
            $tenggang_karyawans = Karyawan::where('status_karyawan', 'AT')
                ->leftJoin('users', 'karyawans.user_id', 'users.id')
                ->where('users.organisasi_id', $user->organisasi_id)
                ->whereRaw('(tanggal_selesai - ?) <= 30', [$today])
                ->selectRaw('*, (tanggal_selesai - ?) as jumlah_hari', [$today])
                ->get();

            $cutie_approval = Cutie::selectRaw('cutis.*, karyawans.nama, (rencana_mulai_cuti - ?) as jumlah_hari',[$today])->leftJoin('karyawans', 'cutis.karyawan_id', 'karyawans.id_karyawan')
                ->leftJoin('users', 'karyawans.user_id', 'users.id')
                ->where('users.organisasi_id', $user->organisasi_id)
                ->where('status_dokumen', 'WAITING')
                ->where(function($query) {
                $query->where('status_cuti', '!=', 'CANCELED')
                      ->orWhereNull('status_cuti');
            })
                ->whereNotNull('approved_by')
                ->whereNull('legalized_by')
                ->get();

            $rejected_cuti = [];
            $agenda_lembur = [];

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
            //My Cuti
            $my_cutie = Cutie::selectRaw('cutis.*, karyawans.nama, (rencana_mulai_cuti - ?) as jumlah_hari',[$today])->leftJoin('karyawans', 'cutis.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoin('karyawan_posisi', 'cutis.karyawan_id', 'karyawan_posisi.karyawan_id')
            ->where('status_dokumen', 'WAITING')
            ->where(function($query) {
                $query->where('status_cuti', '!=', 'CANCELED')
                      ->orWhereNull('status_cuti');
            })
            ->where('cutis.karyawan_id', $me->id_karyawan)
            ->whereRaw('(rencana_mulai_cuti - ?) <= 7', [$today])
            ->get();

            // Notif Approval
            $cutie_approval = Cutie::selectRaw('cutis.*, karyawans.nama, (rencana_mulai_cuti - ?) as jumlah_hari',[$today])->leftJoin('karyawans', 'cutis.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoin('karyawan_posisi', 'cutis.karyawan_id', 'karyawan_posisi.karyawan_id')
            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
            ->where('status_dokumen', 'WAITING')
            ->where(function($query) {
                $query->where('status_cuti', '!=', 'CANCELED')
                      ->orWhereNull('status_cuti');
            })
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

            $agenda_lembur = DetailLembur::where('karyawan_id', auth()->user()->karyawan->id_karyawan)->whereHas('lembur', function ($query) {
                $query->whereIn('status', ['WAITING', 'PLANNED']);
            })->orderBy('rencana_mulai_lembur', 'ASC')->get();

        } elseif (auth()->user()->hasRole('member')) {
            $me = auth()->user()->karyawan;
            $my_cutie = Cutie::selectRaw('cutis.*, karyawans.nama, (rencana_mulai_cuti - ?) as jumlah_hari',[$today])->leftJoin('karyawans', 'cutis.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoin('karyawan_posisi', 'cutis.karyawan_id', 'karyawan_posisi.karyawan_id')
            ->where('status_dokumen', 'WAITING')
            ->where(function($query) {
                $query->where('status_cuti', '!=', 'CANCELED')
                      ->orWhereNull('status_cuti');
            })
            ->where('cutis.karyawan_id', $me->id_karyawan)
            ->whereRaw('(rencana_mulai_cuti - ?) <= 7', [$today])
            ->get();

            $cutie_approval = null;

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

            $agenda_lembur = DetailLembur::where('karyawan_id', auth()->user()->karyawan->id_karyawan)->whereHas('lembur', function ($query) {
                $query->whereIn('status', ['WAITING', 'PLANNED']);
            })->orderBy('rencana_mulai_lembur', 'ASC')->get();
        }

        if (!auth()->user()->hasRole('security')) {
            $notification = [
                'count_notif' => $tenggang_karyawans?->count() + $cutie_approval?->count() + count($rejected_cuti) + $my_cutie?->count(),
                'list' => $tenggang_karyawans->toArray(),
                'my_cutie' => $my_cutie ? $my_cutie->toArray() : [],
                'cutie_approval' => $cutie_approval ? $cutie_approval->toArray() : [],
                'count_my_cutie' => $my_cutie ? $my_cutie->count() : 0,
                'count_cutie_approval' => $cutie_approval ? $cutie_approval->count() : 0,
                'count_rejected_cuti' => count($rejected_cuti),
                'rejected_cuti' => $rejected_cuti,
                'agenda_lembur' => $agenda_lembur ? $agenda_lembur->count() : 0,
            ];
        } else {
            $notification = [
                'count_notif' => 0,
                'list' => [],
                'my_cutie' => [],
                'cutie_approval' => [],
                'count_my_cutie' => 0,
                'count_cutie_approval' => 0,
                'count_rejected_cuti' => 0,
                'rejected_cuti' => [],
                'agenda_lembur' => 0,
            ];
        }
        view()->share('notification', $notification);
        return $next($request);
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
