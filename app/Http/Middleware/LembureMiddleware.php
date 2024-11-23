<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Posisi;
use App\Models\Lembure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LembureMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $has_leader = false;
        $user = auth()->user();
        $organisasi_id = $user->organisasi_id;
        $approval_lembur = 0;
        $pengajuan_lembur= 0;
        
        if(auth()->user()->karyawan && auth()->user()->karyawan->posisi){
            $posisi = auth()->user()->karyawan->posisi;
            $has_leader = $this->has_leader_head($posisi);
            if(!$has_leader || auth()->user()->karyawan->posisi[0]->jabatan_id == 5){
                $pengajuan_lembur = Lembure::where('issued_by', $user->karyawan->id_karyawan)->where('status', 'PLANNED')->count();
            }
        }

        //APPROVAL LEMBUR
        if($user->hasRole('personalia')){
            $approval_lembur = Lembure::where(function($query) {
                $query->where(function($query) {
                    $query->where('status', 'WAITING')
                        ->whereNotNull('plan_approved_by')
                        ->whereNull('plan_legalized_by');
                })->orWhere(function($query) {
                    $query->where('status', 'COMPLETED')
                        ->whereNotNull('actual_approved_by')
                        ->whereNull('actual_legalized_by');
                });
            })->where('organisasi_id', $organisasi_id)->count();
        } elseif ($user->karyawan->posisi[0]->jabatan_id == 2 && $user->karyawan->posisi[0]->organisasi_id !== NULL){ 
            $approval_lembur = Lembure::where(function($query) {
                $query->where(function($query) {
                    $query->where('status', 'WAITING')
                        ->whereNotNull('plan_checked_by')
                        ->whereNull('plan_approved_by');
                })->orWhere(function($query) {
                    $query->where('status', 'COMPLETED')
                        ->whereNotNull('actual_checked_by')
                        ->whereNull('actual_approved_by');
                });
            })->where('organisasi_id', $organisasi_id)->count();
        } elseif ($user->karyawan->posisi[0]->jabatan_id == 4 || $user->karyawan->posisi[0]->jabatan_id == 3) {
            $posisi = $user->karyawan->posisi;
            $member_posisi_ids = $this->get_member_posisi($posisi);
            $approval_lembur = Lembure::leftJoin('karyawan_posisi', 'lemburs.issued_by', 'karyawan_posisi.karyawan_id')
            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')->whereIn('posisis.id_posisi', $member_posisi_ids)
            ->where(function($query) {
                $query->where(function($query) {
                    $query->where('status', 'WAITING')
                        ->whereNull('plan_checked_by');
                })->orWhere(function($query) {
                    $query->where('status', 'COMPLETED')
                        ->whereNull('actual_checked_by');
                });
            })->count();
        }

        $lembure = [
            'has_leader' => $has_leader,
            'is_leader' => $this->is_leader(),
            'approval_lembur' => $approval_lembur,
            'pengajuan_lembur' => $pengajuan_lembur
        ];
        
        view()->share('lembure', $lembure);
        return $next($request);
    }

    function is_leader(){
        $is_leader = false;
        if(auth()->user()->karyawan && auth()->user()->karyawan->posisi[0]->jabatan_id == 5){
            $is_leader = true;
        }
        return $is_leader;
    }

    function has_leader_head($posisi)
    {
        $has_leader = false;
        if($posisi){
            foreach($posisi as $pos){
                $parent_posisi_ids = $this->get_parent_posisi($pos);
                if(!empty($parent_posisi_ids)){
                    foreach ($parent_posisi_ids as $parent_id){
                        if($parent_id !== 0){
                            if(Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 5){
                                $has_leader = true;
                            }
                        }
                    }
                }
            }
        } else {
            return response()->json(['message' => 'Anda tidak memiliki posisi, silahkan hubungi HRD'], 200);
        }

        return $has_leader;
    } 

    function get_parent_posisi($posisi)
    {
        $data = [];
        if ($posisi->parent_id !== 0) {
            $parent = Posisi::find($posisi->parent_id);
            $data = array_merge($data, $this->get_parent_posisi($parent));
        }
        $data[] = $posisi->parent_id;
        return $data;
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
