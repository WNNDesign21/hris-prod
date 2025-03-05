<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\Approval;
use Illuminate\Http\Request;
use App\Models\TugasLuare\TugasLuar;
use Symfony\Component\HttpFoundation\Response;

class TugasluarMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $pengajuan = 0;
        $approval = 0;
        $organisasi_id = auth()->user()->organisasi_id;
        
        // APPROVAL PERSONALIA
        if (auth()->user()->hasRole('personalia')) {
            $dataFilter['organisasi_id'] = $organisasi_id;
            $dataFilter['must_legalized'] = true;
            $approval = TugasLuar::countDataMiddleware($dataFilter);
        } 
        
        // APPROVAL ATASAN
        if (auth()->user()->hasRole('atasan')) {
            $posisi = auth()->user()->karyawan->posisi;
            $id_posisi_members = Approval::GetMemberPosisi($posisi);
            $has_leader = Approval::HasLeader($posisi);
            $has_section_head = Approval::HasSectionHead($posisi);
            $has_department_head = Approval::HasDepartmentHead($posisi);

            foreach ($posisi as $ps){
                $index = array_search($ps->id_posisi, $id_posisi_members);
                array_splice($id_posisi_members, $index, 1);
            }

            $dataFilter['member_posisi_id'] = $id_posisi_members;
            if(auth()->user()->karyawan->posisi[0]->jabatan_id == 5) {
                if (!$has_section_head && !$has_department_head) {
                    $dataFilter['must_checked'] = true;
                }
            } else {
                $dataFilter['must_checked'] = true;
            }

            $approval = TugasLuar::countDataMiddleware($dataFilter);
        } 

        // PENGAJUAN
        if (auth()->user()->hasRole('atasan') || auth()->user()->hasRole('member')) {
            $dataFilter['karyawan_id'] = auth()->user()->karyawan->id_karyawan;
            $pengajuan = TugasLuar::countDataMiddleware($dataFilter);
        }

        $tugasluars = [
            'pengajuan' => $pengajuan,
            'approval' => $approval,
        ];
        
        view()->share('tugasluare', $tugasluars);
        return $next($request);
    }
}
