<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Divisi;
use App\Models\Posisi;
use App\Models\Lembure;
use App\Helpers\Approval;
use App\Models\DetailLembur;
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
        $has_dept_head = false;
        $user = auth()->user();
        $organisasi_id = $user->organisasi_id;
        $approval_lembur = 0;
        $pengajuan_lembur= 0;
        $review_lembur = 0;
        
        if(auth()->user()->karyawan && auth()->user()->karyawan->posisi){
            $posisi = auth()->user()->karyawan->posisi;
            $list_atasan = Approval::ListAtasan($posisi);
            $has_leader = $list_atasan['leader'] ? true : false;
            $has_dept_head = $list_atasan['department_head'] ? true : false;
            if(!$has_leader || auth()->user()->karyawan->posisi[0]->jabatan_id == 5){
                $pengajuan_lembur = Lembure::where('issued_by', $user->karyawan->id_karyawan)->where('status', 'PLANNED')->count();
            }
        }

        //APPROVAL LEMBUR
        if($user->hasAnyRole(['personalia', 'personalia-lembur'])){
            $approval_lembur = Lembure::where(function($query) {
                $query->where(function($query) {
                    $query->where('status', 'WAITING')
                        ->whereNotNull('plan_approved_by')
                        ->whereNotNull('plan_reviewed_by')
                        ->whereNull('plan_legalized_by');
                })->orWhere(function($query) {
                    $query->where('status', 'COMPLETED')
                        ->whereNotNull('actual_approved_by')
                        ->whereNotNull('actual_reviewed_by')
                        ->whereNull('actual_legalized_by');
                });
            })->where('organisasi_id', $organisasi_id)->count();
        } elseif ($user->karyawan && $user->karyawan->posisi[0]->jabatan_id == 2){ 
            if (auth()->user()->karyawan->posisi[0]->divisi_id == 3) {
                $posisis_has_div_head = Posisi::where('jabatan_id', 2)
                ->whereHas('karyawan')
                ->whereNot('divisi_id', 3)
                ->where(function ($query) {
                    $query->whereNull('organisasi_id')
                        ->orWhere('organisasi_id', auth()->user()->organisasi_id);
                })
                ->distinct()
                ->pluck('divisi_id')
                ->toArray();
                $divisis = Divisi::whereNotIn('id_divisi', $posisis_has_div_head)->pluck('id_divisi');
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
                    })
                    ->where('organisasi_id', $organisasi_id)
                    ->whereIn('divisi_id', $divisis)
                    ->count();
            } else {
                $posisi = $user->karyawan->posisi;
                $member_posisi_ids = $this->get_member_posisi($posisi);
                $approval_lembur = Lembure::leftJoin('karyawan_posisi', 'lemburs.issued_by', 'karyawan_posisi.karyawan_id')
                ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')->whereIn('posisis.id_posisi', $member_posisi_ids)
                ->where(function($query) {
                    $query->where(function($query) {
                        $query->where('status', 'WAITING')
                            ->whereNotNull('plan_checked_by')
                            ->whereNull('plan_approved_by');
                    })->orWhere(function($query) {
                        $query->where('status', 'COMPLETED')
                            ->whereNotNull('actual_checked_by')
                            ->whereNull('actual_approved_by');
                    });
                })->count();
            }
        } elseif ($user->karyawan && $user->karyawan->posisi[0]->jabatan_id == 4 || $user->karyawan && $user->karyawan->posisi[0]->jabatan_id == 3) {
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
        } elseif ($user->karyawan && $user->karyawan->posisi[0]->jabatan_id == 1) {
            $posisi = auth()->user()->karyawan->posisi;
            $departemen_ids = $this->get_member_departemen($posisi);

            foreach ($posisi as $ps){
                $index = array_search($ps->departemen_id, $departemen_ids);
                array_splice($departemen_ids, $index, 1);
            }
            array_push($departemen_ids, auth()->user()->karyawan->posisi[0]->departemen_id);
            $departemen_ids = array_filter(array_unique($departemen_ids));
            sort($departemen_ids);

            $review_lembur = DetailLembur::selectRaw('
                detail_lemburs.organisasi_id,
                detail_lemburs.departemen_id,
                departemens.nama as departemen,
                divisis.nama as divisi,
                organisasis.nama as organisasi,
                detail_lemburs.divisi_id,
                CASE WHEN (lemburs.status = '."'WAITING'".' AND lemburs.plan_approved_by IS NOT NULL) THEN '."'PLANNING'".' ELSE '."'ACTUAL'".' END AS status,
                DATE(detail_lemburs.rencana_mulai_lembur) AS tanggal_lembur,
                SUM(detail_lemburs.nominal) as total_nominal_lembur,
                SUM(detail_lemburs.durasi) as total_durasi_lembur,
                COUNT(detail_lemburs.karyawan_id) as total_karyawan,
                COUNT(DISTINCT detail_lemburs.lembur_id) as total_dokumen
            ')
            ->leftJoin('lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')
            ->leftJoin('departemens', 'departemens.id_departemen', 'detail_lemburs.departemen_id')
            ->leftJoin('organisasis', 'organisasis.id_organisasi', 'detail_lemburs.organisasi_id')
            ->leftJoin('divisis', 'divisis.id_divisi', 'detail_lemburs.divisi_id')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('lemburs.status','WAITING');
                    $query->whereNotNull('lemburs.plan_approved_by');
                    $query->whereNull('lemburs.plan_reviewed_by');
                    $query->whereNull('lemburs.plan_legalized_by');
                });
                $query->orWhere(function ($query) {
                    $query->where('lemburs.status', 'COMPLETED');
                    $query->whereNotNull('lemburs.actual_approved_by');
                    $query->whereNull('lemburs.actual_reviewed_by');
                    $query->whereNull('lemburs.actual_legalized_by');
                });
            })
            ->whereIn('detail_lemburs.departemen_id', $departemen_ids)
            ->where('detail_lemburs.is_aktual_approved', 'Y')
            ->groupBy('detail_lemburs.organisasi_id', 'detail_lemburs.departemen_id', 'detail_lemburs.divisi_id', 'departemens.nama', 'divisis.nama', 'organisasis.nama', 'tanggal_lembur', 'lemburs.plan_approved_by', 'lemburs.status')
            ->get()->count();
        }

        $lembure = [
            'has_leader' => $has_leader,
            'has_dept_head' => $has_dept_head,
            'is_leader' => $this->is_leader(),
            'approval_lembur' => $approval_lembur,
            'pengajuan_lembur' => $pengajuan_lembur,
            'review_lembur' => $review_lembur
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

    function get_member_departemen($posisis)
    {
        $data = [];
        foreach ($posisis as $ps) {
            if ($ps->children) {
                $data = array_merge($data, $this->get_member_departemen($ps->children));
            }
            $data[] = $ps->departemen_id;
        }
        return $data;
    }
}
