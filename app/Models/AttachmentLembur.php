<?php

namespace App\Models;

use App\Models\Lembure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttachmentLembur extends Model
{
    use HasFactory;

    protected $table = 'attachment_lemburs';
    protected $primaryKey = 'id_attachment_lembur';

    protected $fillable = [
        'lembur_id','path'
    ];

    public function lembur()
    {
        $this->belongsTo(Lembure::class, 'lembur_id', 'id_lembur');
    }
}
