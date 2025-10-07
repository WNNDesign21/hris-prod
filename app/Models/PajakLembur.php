<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PajakLembur extends Model
{
    use HasFactory;

    protected $table = 'pajak_lemburs';

    protected $fillable = [
        'karyawan_id',
        'periode',
        'potongan_pph',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }
}
