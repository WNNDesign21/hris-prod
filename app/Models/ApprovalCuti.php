<?php

namespace App\Models;

use App\Models\Cutie;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApprovalCuti extends Model
{
    use HasFactory;

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
}
