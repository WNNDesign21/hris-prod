<?php

namespace App\Helpers;

use App\Models\Posisi;

/**
 * Format response.
 */
class Approval
{
    /**
     * API Response
     *
     * @var array
     */
    protected static $array = [];
    protected static $atasan = [];
    protected static $member = [];
    protected static $response = false;
    protected static $has_leader = null;
    protected static $has_section_head = null;
    protected static $has_department_head = null;
    protected static $has_division_head = null;
    protected static $has_director = null;

    public static function HasDirector($posisi)
    {
        self::$response = false;
        if($posisi){
            foreach($posisi as $pos){
                $parent_posisi_ids = self::GetParentPosisi($pos);
                if(!empty($parent_posisi_ids)){
                    foreach ($parent_posisi_ids as $parent_id){
                        if($parent_id !== 0){
                            if(Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 1){
                                self::$response = true;
                            }
                        }
                    }
                }
            }
        }

        return self::$response;
    }

    public static function HasDivisionHead($posisi)
    {
        self::$response = false;
        if($posisi){
            foreach($posisi as $pos){
                $parent_posisi_ids = self::GetParentPosisi($pos);
                if(!empty($parent_posisi_ids)){
                    foreach ($parent_posisi_ids as $parent_id){
                        if($parent_id !== 0){
                            if(Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 2){
                                self::$response = true;
                            }
                        }
                    }
                }
            }
        }

        return self::$response;
    }

    public static function HasDepartmentHead($posisi)
    {
        self::$response = false;
        if($posisi){
            foreach($posisi as $pos){
                $parent_posisi_ids = self::GetParentPosisi($pos);
                if(!empty($parent_posisi_ids)){
                    foreach ($parent_posisi_ids as $parent_id){
                        if($parent_id !== 0){
                            if(Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 3){
                                self::$response = true;
                            }
                        }
                    }
                }
            }
        }

        return self::$response;
    }

    public static function HasSectionHead($posisi)
    {
        self::$response = false;
        if($posisi){
            foreach($posisi as $pos){
                $parent_posisi_ids = self::GetParentPosisi($pos);
                if(!empty($parent_posisi_ids)){
                    foreach ($parent_posisi_ids as $parent_id){
                        if($parent_id !== 0){
                            if(Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 4){
                                self::$response = true;
                            }
                        }
                    }
                }
            }
        }

        return self::$response;
    }

    public static function HasLeader($posisi)
    {
        self::$response = false;
        if($posisi){
            foreach($posisi as $pos){
                $parent_posisi_ids = self::GetParentPosisi($pos);
                if(!empty($parent_posisi_ids)){
                    foreach ($parent_posisi_ids as $parent_id){
                        if($parent_id !== 0){
                            if(Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 5){
                                self::$response = true;
                            }
                        }
                    }
                }
            }
        }

        return self::$response;
    }

    public static function GetParentPosisi($posisi)
    {
        self::$array = [];
        if ($posisi->parent_id !== 0) {
            $parent = Posisi::find($posisi->parent_id);
            self::$array = array_merge(self::$array, self::GetParentPosisi($parent));
        }
        self::$array[] = $posisi->parent_id;
        return self::$array;
    }

    public static function GetMemberPosisi($posisi)
    {
        self::$array = [];
        foreach ($posisi as $ps) {
            if ($ps->children) {
                self::$array = array_merge(self::$array, self::GetMemberPosisi($ps->children));
            }
            self::$array[] = $ps->id_posisi;
        }
        return self::$array;
    }

    //UNTUK CUTI
     public static function HasDirectorCuti($posisi)
     {
         self::$has_director = null;
         if($posisi){
             foreach($posisi as $pos){
                 $parent_posisi_ids = self::GetParentPosisi($pos);
                 if(!empty($parent_posisi_ids)){
                     foreach ($parent_posisi_ids as $parent_id){
                         if($parent_id !== 0){
                             if(Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 1){
                                 self::$has_director = $parent_id;
                                 break;
                             }
                         }
                     }
                 }
             }
         }

         return self::$has_director;
     }

    public static function HasDivisionHeadCuti($posisi)
    {
        self::$has_division_head = null;
        if($posisi){
            foreach($posisi as $pos){
                $parent_posisi_ids = self::GetParentPosisi($pos);
                if(!empty($parent_posisi_ids)){
                    foreach ($parent_posisi_ids as $parent_id){
                        if($parent_id !== 0){
                            if(Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 2){
                                self::$has_division_head = $parent_id;
                                break;
                            }
                        }
                    }
                }
            }
        }

        return self::$has_division_head;
    }

    public static function HasDepartmentHeadCuti($posisi)
    {
        self::$has_department_head = null;
        if($posisi){
            foreach($posisi as $pos){
                $parent_posisi_ids = self::GetParentPosisi($pos);
                if(!empty($parent_posisi_ids)){
                    foreach ($parent_posisi_ids as $parent_id){
                        if($parent_id !== 0){
                            if(Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 3){
                                self::$has_department_head = $parent_id;
                                break;
                            }
                        }
                    }
                }
            }
        }

        return self::$has_department_head;
    }

    public static function HasSectionHeadCuti($posisi)
    {
        self::$has_section_head = null;
        if($posisi){
            foreach($posisi as $pos){
                $parent_posisi_ids = self::GetParentPosisi($pos);
                if(!empty($parent_posisi_ids)){
                    foreach ($parent_posisi_ids as $parent_id){
                        if($parent_id !== 0){
                            if(Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 4){
                                self::$has_section_head = $parent_id;
                                break;
                            }
                        }
                    }
                }
            }
        }

        return self::$has_section_head;
    }

    public static function HasLeaderCuti($posisi)
    {
        self::$has_leader = null;
        if($posisi){
            foreach($posisi as $pos){
                $parent_posisi_ids = self::GetParentPosisi($pos);
                if(!empty($parent_posisi_ids)){
                    foreach ($parent_posisi_ids as $parent_id){
                        if($parent_id !== 0){
                            if(Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 5){
                                self::$has_leader = $parent_id;
                            }
                        }
                    }
                }
            }
        }

        return self::$has_leader;
    }

    public static function ListAtasan($posisi)
    {
        self::$atasan = ['leader' => null, 'section_head' => null, 'department_head' => null, 'division_head' =>  null, 'director' => null];
        if($posisi){
            foreach($posisi as $pos){
                $parent_posisi_ids = self::GetParentPosisi($pos);
                if(!empty($parent_posisi_ids)){
                    foreach ($parent_posisi_ids as $parent_id){
                        if($parent_id !== 0){
                            if(Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 5){
                                self::$atasan['leader'] = $parent_id;
                            } elseif (Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 4){
                                self::$atasan['section_head'] = $parent_id;
                            } elseif (Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 3){
                                self::$atasan['department_head'] = $parent_id;
                            } elseif (Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 2){
                                self::$atasan['division_head'] = $parent_id;
                            } elseif (Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 1){
                                self::$atasan['director'] = $parent_id;
                            }
                        }
                    }
                }
            }
        }
        return self::$atasan;
    }

    public static function ListMember($posisi)
    {
        self::$member = ['leader' => null, 'section_head' => null, 'department_head' => null, 'division_head' =>  null, 'director' => null];
        if($posisi){
            foreach($posisi as $pos){
                $member_posisi_ids = self::GetMemberPosisi($pos);
                if(!empty($member_posisi_ids)){
                    foreach ($member_posisi_ids as $member_id){
                        if($member_id !== 0){
                            if(Posisi::where('id_posisi', $member_id)->first()->jabatan_id == 5){
                                self::$member['leader'] = $member_id;
                            } elseif (Posisi::where('id_posisi', $member_id)->first()->jabatan_id == 4){
                                self::$member['section_head'] = $member_id;
                            } elseif (Posisi::where('id_posisi', $member_id)->first()->jabatan_id == 3){
                                self::$member['department_head'] = $member_id;
                            } elseif (Posisi::where('id_posisi', $member_id)->first()->jabatan_id == 2){
                                self::$member['division_head'] = $member_id;
                            } elseif (Posisi::where('id_posisi', $member_id)->first()->jabatan_id == 1){
                                self::$member['director'] = $member_id;
                            }
                        }
                    }
                }
            }
        }
        return self::$member;
    }

    public static function ApprovalDeptWithPlantHead($parent_id, $organisasi_id)
    {
        self::$atasan = ['leader' => null, 'section_head' => null, 'department_head' => null, 'division_head' =>  null, 'plant_head' => Posisi::where('jabatan_id', 2)->where('organisasi_id', auth()->user()->organisasi_id)->where('nama','ILIKE','PLANT%')->first()->id_posisi, 'director' => null];
        $posisi = Posisi::where('parent_id', $parent_id)->get();
        if($posisi){
            foreach($posisi as $pos){
                $parent_posisi_ids = self::GetParentPosisi($pos);
                if(!empty($parent_posisi_ids)){
                    foreach ($parent_posisi_ids as $parent_id){
                        if($parent_id !== 0){
                            if(Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 5){
                                self::$atasan['leader'] = $parent_id;
                            } elseif (Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 4){
                                self::$atasan['section_head'] = $parent_id;
                            } elseif (Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 3){
                                self::$atasan['department_head'] = $parent_id;
                            } elseif (Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 2){
                                self::$atasan['division_head'] = $parent_id;
                            } elseif (Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 1){
                                self::$atasan['director'] = $parent_id;
                            }
                        }
                    }
                }
            }
        }
        return self::$atasan;
    }

}
