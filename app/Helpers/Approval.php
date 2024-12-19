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
    protected static $approval = [];
    protected static $response = false;
    protected static $has_leader = null;
    protected static $has_section_head = null;
    protected static $has_department_head = null;
    protected static $has_division_head = null;
    protected static $has_director = null;

    /**
     * Give success response.
     */
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

    /**
     * Give error response.
     */
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

    //FOR TEST
     /**
     * Give success response.
     */
     public static function HasDirectorTest($posisi)
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

    public static function HasDivisionHeadTest($posisi)
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

    public static function HasDepartmentHeadTest($posisi)
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

    /**
     * Give error response.
     */
    public static function HasSectionHeadTest($posisi)
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

    public static function HasLeaderTest($posisi)
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
}
