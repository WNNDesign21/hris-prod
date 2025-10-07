<?php

namespace App\Models\KSK;

use App\Models\KSK\DetailKSK;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttachmentKSKDetail extends Model
{
    use HasFactory;
    protected $table = 'attachment_ksk_details';
    protected $primaryKey = 'id_attachment_ksk_detail';

    protected $fillable = [
        'ksk_detail_id','path'
    ];

    public function kskDetail()
    {
        $this->belongsTo(DetailKSK::class, 'ksk_detail_id', 'id_ksk_detail');
    }
}
