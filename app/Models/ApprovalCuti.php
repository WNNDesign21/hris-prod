<?php

namespace App\Models;

use Throwable;
use App\Models\Cutie;
use App\Helpers\Approval;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApprovalCuti extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'approval_cutis';
    protected $primaryKey = 'id_approval_cuti';

    protected $fillable = [
        'cuti_id',
        'checked1_for', 'checked1_by', 'checked1_karyawan_id', 
        'checked2_for', 'checked2_by','checked2_karyawan_id', 
        'approved_for', 'approved_by', 'approved_karyawan_id'
    ];

    public function cuti()
    {
        return $this->belongsTo(Cutie::class, 'cuti_id', 'id_cuti');
    }

    public static function storeApprovalCuti($cuti_id, $posisi)
    {
        try{
            $my_jabatan = $posisi[0]->jabatan_id;
            $list_atasan = Approval::ListAtasan($posisi);
            $has_leader = $list_atasan['leader'] ?? null;
            $has_section_head = $list_atasan['section_head'] ?? null;
            $has_department_head = $list_atasan['department_head'] ?? null;
            $has_division_head = $list_atasan['division_head'] ?? null;
            $has_director = $list_atasan['director'] ?? null;
    
            $checked1_for = null;
            $checked2_for = null;
            $approved_for = null;
    
            //KONDISI 1 (PUNYA SEMUA)
            if($has_leader && $has_section_head && $has_department_head){
                $checked1_for = $has_leader;
                $checked2_for = $has_section_head;
                $approved_for = $has_department_head;
            }
    
            //KONDISI 2 (HANYA PUNYA LEADER & SECTION HEAD)
            if($has_leader && $has_section_head && !$has_department_head){
                $checked1_for = $has_leader;
                $checked2_for = $has_section_head;
                $approved_for = $has_section_head;
            }
    
            //KONDISI 3 (HANYA PUNYA LEADER DAN DEPARTMENT HEAD)
            if($has_leader && !$has_section_head && $has_department_head){
                $checked1_for = $has_leader;
                $checked2_for = $has_department_head;
                $approved_for = $has_department_head;
            }
    
            //KONDISI 4 (HANYA PUNYA DEPARTMENT HEAD)
            if(!$has_leader && !$has_section_head && $has_department_head){
                $checked1_for = $has_department_head;
                $checked2_for = $has_department_head;
                $approved_for = $has_department_head;
            }
            
            //KONDISI 5 (HANYA PUNYA SECTION HEAD)
            if(!$has_leader && $has_section_head && !$has_department_head){
                $checked1_for = $has_section_head;
                $checked2_for = $has_section_head;
                $approved_for = $has_section_head;
            }
    
            //KONDISI 6 (HANYA PUNYA SECTION HEAD DAN DEPARTMENT HEAD)
            if(!$has_leader && $has_section_head && $has_department_head){
                $checked1_for = $has_section_head;
                $checked2_for = $has_section_head;
                $approved_for = $has_department_head;
            }
    
            //KONDISI 7 (HANYA PUNYA DIVISION HEAD)
            if(!$has_leader && !$has_section_head && !$has_department_head){
                $checked1_for = $has_division_head;
                $checked2_for = $has_division_head;
                $approved_for = $has_division_head;
            }
    
            //KONDISI 8 (HANYA PUNYA DIRECTOR)
            if(!$has_leader && !$has_section_head && !$has_department_head && $my_jabatan == 2){
                $checked1_for = $has_director;
                $checked2_for = $has_director;
                $approved_for = $has_director;
            }
    
            $approval = self::create([
                'cuti_id' => $cuti_id,
                'checked1_for' => $checked1_for,
                'checked2_for' => $checked2_for,
                'approved_for' => $approved_for,
            ]);
            return $approval;
        } catch (Throwable $e){
            return response()->json($e->getMessage(), 500);
        }
    }
}
