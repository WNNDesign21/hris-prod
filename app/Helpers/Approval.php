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
    protected static $response = false;
    protected static $array = [];

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
        foreach ($posisis as $ps) {
            if ($ps->children) {
                self::$array = array_merge(self::$array, self::GetMemberPosisi($ps->children));
            }
            self::$array[] = $ps->id_posisi;
        }
        return self::$array;
    }
}
