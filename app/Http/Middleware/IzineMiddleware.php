<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Izine;
use App\Models\Posisi;
use App\Models\Sakite;
use App\Helpers\Approval;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IzineMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $has_leader = false;
        $has_section_head = false;
        $has_department_head = false;
        $organisasi_id = $user->organisasi_id;
        $pengajuan_izin = 0;
        $approval_izin = 0;
        $lapor_skd = 0;
        $approval_skd = 0;

        if($user->karyawan && $user->karyawan->posisi){
            $pengajuan_izin = Izine::where('karyawan_id', $user->karyawan->id_karyawan)
            ->where(function($query) {
                $query->whereNull('rejected_by')->whereNotNull('legalized_by')
                ->where(function($query) {
                    $query->where(function($query) {
                        $query->whereIn('jenis_izin', ['TM', 'SH']);
                        $query->whereNull('aktual_mulai_or_masuk');
                        $query->whereNull('aktual_selesai_or_keluar');
                    })->orWhere(function($query){
                        $query->where('jenis_izin', 'KP');
                        $query->whereNull('aktual_mulai_or_masuk');
                    })->orWhere(function($query){
                        $query->where('jenis_izin', 'PL');
                        $query->whereNull('aktual_selesai_or_keluar');
                    });
                });
            })->count();

            $lapor_skd = Sakite::where('karyawan_id', $user->karyawan->id_karyawan)->whereNull('rejected_by')->whereNull('attachment')->count();
        }

        //HRD
        if ($user->hasRole('personalia')){
            $approval_izin = Izine::where('organisasi_id', $organisasi_id)->whereNull('rejected_by')->whereNull('legalized_by')->whereNotNull('approved_by')->count();
            $approval_skd = Sakite::where('organisasi_id', $organisasi_id)->whereNull('rejected_by')->whereNull('legalized_by')->whereNotNull('approved_by')->whereNotNull('attachment')->count();
        }

        //leader
        if ($user->hasRole('atasan') && $user->karyawan->posisi[0]->jabatan_id == 5){
            $posisi = $user->karyawan->posisi;
            $id_posisi_members = Approval::GetMemberPosisi($posisi);

            foreach ($posisi as $ps){
                $index = array_search($ps->id_posisi, $id_posisi_members);
                array_splice($id_posisi_members, $index, 1);
            }

            foreach ($posisi as $ps){
                $index = array_search($ps->id_posisi, $id_posisi_members);
                array_splice($id_posisi_members, $index, 1);
            }

            $approval_izin = Izine::leftJoin('karyawan_posisi', 'izins.karyawan_id', 'karyawan_posisi.karyawan_id')
                            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
                            ->whereIn('posisis.id_posisi', $id_posisi_members)
                            ->whereNull('rejected_by')
                            ->whereNull('checked_by')
                            ->count();

            $approval_skd = Sakite::leftJoin('karyawan_posisi', 'sakits.karyawan_id', 'karyawan_posisi.karyawan_id')
                            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
                            ->whereIn('posisis.id_posisi', $id_posisi_members)
                            ->whereNull('rejected_by')
                            ->whereNull('approved_by')
                            // ->whereNotNull('attachment')
                            ->count();
        }

        //section head
        if ($user->hasRole('atasan') && $user->karyawan->posisi[0]->jabatan_id == 4){
            $posisi = $user->karyawan->posisi;
            $my_posisi = $posisi[0]->jabatan_id;
            $id_posisi_members = Approval::GetMemberPosisi($posisi);

            foreach ($posisi as $ps){
                $index = array_search($ps->id_posisi, $id_posisi_members);
                array_splice($id_posisi_members, $index, 1);
            }

            $izins = Izine::leftJoin('karyawan_posisi', 'izins.karyawan_id', 'karyawan_posisi.karyawan_id')
                    ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
                    ->whereIn('posisis.id_posisi', $id_posisi_members)
                    ->whereNull('rejected_by')
                    ->where(function($query){
                        $query->whereNull('legalized_by')
                        ->where(function($query){
                            $query->whereNull('checked_by');
                            $query->orWhereNull('approved_by');
                        });
                    })->get();

            foreach ($izins as $izin){
                $posisi = $izin->karyawan->posisi;
                $has_leader = $this->has_leader($posisi);
                $has_section_head = $this->has_section_head($posisi);
                $has_department_head = $this->has_department_head($posisi);

                if (!$has_leader) {
                    $approval_izin++;
                }

                if ($has_leader && !$izin->approved_by){
                    $approval_izin++;
                }
            }

            $approval_skd = Sakite::leftJoin('karyawan_posisi', 'sakits.karyawan_id', 'karyawan_posisi.karyawan_id')
                    ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
                    ->whereIn('posisis.id_posisi', $id_posisi_members)
                    ->whereNull('rejected_by')
                    ->whereNull('approved_by')
                    ->count();
        }

        //department head
        if ($user->hasRole('atasan') && $user->karyawan->posisi[0]->jabatan_id == 3){
            $posisi = $user->karyawan->posisi;
            $my_posisi = $posisi[0]->jabatan_id;
            $id_posisi_members = Approval::GetMemberPosisi($posisi);

            foreach ($posisi as $ps){
                $index = array_search($ps->id_posisi, $id_posisi_members);
                array_splice($id_posisi_members, $index, 1);
            }

            $izins = Izine::leftJoin('karyawan_posisi', 'izins.karyawan_id', 'karyawan_posisi.karyawan_id')
                    ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
                    ->whereIn('posisis.id_posisi', $id_posisi_members)
                    ->where(function ($query) {
                        $query->whereNull('izins.checked_by')
                            ->orWhereNull('izins.approved_by');
                    })->whereNull('izins.legalized_by')
                    ->whereNull('izins.rejected_by')
                    ->get();

            foreach ($izins as $izin){
                $posisi = $izin->karyawan->posisi;
                $has_leader = $this->has_leader($posisi);
                $has_section_head = $this->has_section_head($posisi);
                $has_department_head = $this->has_department_head($posisi);

                if (!$has_leader && !$has_section_head) {
                    $approval_izin++;
                }

                if ($has_leader && !$has_section_head && !$izin->approved_by){
                    $approval_izin++;
                }

                if (!$has_leader && $has_section_head && !$izin->approved_by){
                    $approval_izin++;
                }
            }

            $skds = Sakite::leftJoin('karyawan_posisi', 'sakits.karyawan_id', 'karyawan_posisi.karyawan_id')
                    ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
                    ->whereIn('posisis.id_posisi', $id_posisi_members)
                    ->whereNull('rejected_by')
                    ->whereNull('approved_by')
                    ->get();

            foreach ($skds as $skd){
                $posisi = $skd->karyawan->posisi;
                $has_leader = $this->has_leader($posisi);
                $has_section_head = $this->has_section_head($posisi);
                $has_department_head = $this->has_department_head($posisi);

                if (!$has_section_head) {
                    $approval_skd++;
                }
            }
        }

        //plant head
        if ($user->hasRole('atasan') && $user->karyawan->posisi[0]->jabatan_id == 2){
            $posisi = $user->karyawan->posisi;
            $my_posisi = $posisi[0]->jabatan_id;
            $id_posisi_members = Approval::GetMemberPosisi($posisi);

            foreach ($posisi as $ps){
                $index = array_search($ps->id_posisi, $id_posisi_members);
                array_splice($id_posisi_members, $index, 1);
            }

            $izins = Izine::leftJoin('karyawan_posisi', 'izins.karyawan_id', 'karyawan_posisi.karyawan_id')
                    ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
                    ->whereIn('posisis.id_posisi', $id_posisi_members)
                    ->where(function ($query) {
                        $query->whereNull('izins.checked_by')
                            ->orWhereNull('izins.approved_by');
                    })->whereNull('izins.legalized_by')
                    ->whereNull('izins.rejected_by')
                    ->get();

            foreach ($izins as $izin){
                $posisi = $izin->karyawan->posisi;
                $has_leader = $this->has_leader($posisi);
                $has_section_head = $this->has_section_head($posisi);
                $has_department_head = $this->has_department_head($posisi);

                if (!$has_leader && !$has_section_head && !$has_department_head) {
                    $approval_izin++;
                }

                if ($has_leader && !$has_section_head && !$has_department_head && !$izin->approved_by){
                    $approval_izin++;
                }
            }

            $skds = Sakite::leftJoin('karyawan_posisi', 'sakits.karyawan_id', 'karyawan_posisi.karyawan_id')
                    ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
                    ->whereIn('posisis.id_posisi', $id_posisi_members)
                    ->whereNull('rejected_by')
                    ->whereNull('approved_by')
                    // ->whereNotNull('attachment')
                    ->get();

            foreach ($skds as $skd){
                $posisi = $skd->karyawan->posisi;
                $has_leader = $this->has_leader($posisi);
                $has_section_head = $this->has_section_head($posisi);
                $has_department_head = $this->has_department_head($posisi);

                if (!$has_section_head && !$has_department_head) {
                    $approval_skd++;
                }
            }
        }

        $izine = [
            'pengajuan_izin' => $pengajuan_izin,
            'approval_izin' => $approval_izin,
            'approval_skd' => $approval_skd,
            'lapor_skd' => $lapor_skd,
            'total_izine_notification' => $pengajuan_izin + $approval_izin + $approval_skd + $lapor_skd
        ];

        view()->share('izine', $izine);
        return $next($request);
    }

    function has_department_head($posisi)
    {
        $has_dept_head = false;
        if($posisi){
            foreach($posisi as $pos){
                $parent_posisi_ids = $this->get_parent_posisi($pos);
                if(!empty($parent_posisi_ids)){
                    foreach ($parent_posisi_ids as $parent_id){
                        if($parent_id !== 0){
                            if(Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 3){
                                $has_dept_head = true;
                            }
                        }
                    }
                }
            }
        } else {
            return response()->json(['message' => 'Anda tidak memiliki posisi, silahkan hubungi HRD'], 200);
        }

        return $has_dept_head;
    }

    function has_section_head($posisi)
    {
        $has_sec_head = false;
        if($posisi){
            foreach($posisi as $pos){
                $parent_posisi_ids = $this->get_parent_posisi($pos);
                if(!empty($parent_posisi_ids)){
                    foreach ($parent_posisi_ids as $parent_id){
                        if($parent_id !== 0){
                            if(Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 4){
                                $has_sec_head = true;
                            }
                        }
                    }
                }
            }
        } else {
            return response()->json(['message' => 'Anda tidak memiliki posisi, silahkan hubungi HRD'], 200);
        }

        return $has_sec_head;
    }

    function has_leader($posisi)
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
