<?php

namespace App\Models;

use App\Models\Posisi;
use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kontrak extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kontraks';
    
    protected $primaryKey = 'id_kontrak';
    public $incrementing = false;

    protected $fillable = [
        'id_kontrak',
        'karyawan_id',
        'posisi_id',
        'nama_posisi',
        'no_surat',
        'tempat_administrasi',
        'issued_date',
        'jenis',
        'status',
        'durasi',
        'salary',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_change_by',
        'status_change_date',
        'attachment'
    ];
    
    protected $dates = [
        'tanggal_mulai',
        'tanggal_selesai',
        'issued_date',
        'status_change_date'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }

    public function posisi()
    {
        return $this->belongsTo(Posisi::class, 'posisi_id', 'id_posisi');
    }

    private static function _query($dataFilter)
    {

        $data = self::select(
            'id_kontrak',
            'karyawan_id',
            'posisi_id',
            'kontraks.nama_posisi',
            'no_surat',
            'tempat_administrasi',
            'issued_date',
            'durasi',
            'salary',
            'deskripsi',
            'tanggal_mulai',
            'tanggal_selesai',
            'jenis',
            'status',
            'status_change_by',
            'status_change_date',
            'attachment',
            'karyawans.nama as nama_karyawan',
        )
        ->leftJoin('karyawans', 'kontraks.karyawan_id', 'karyawans.id_karyawan');

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('karyawans.nama', 'ILIKE', "%{$search}%")
                ->orWhere('kontraks.nama_posisi', 'ILIKE', "%{$search}%")
                ->orWhere('durasi', 'ILIKE', "%{$search}%")
                ->orWhere('status', 'ILIKE', "%{$search}%")
                ->orWhere('status_change_by', 'ILIKE', "%{$search}%")
                ->orWhere('salary', 'ILIKE', "%{$search}%")
                ->orWhere('tanggal_mulai', 'ILIKE', "%{$search}%")
                ->orWhere('tanggal_selesai', 'ILIKE', "%{$search}%")
                ->orWhere('issued_date', 'ILIKE', "%{$search}%")
                ->orWhere('id_kontrak', 'ILIKE', "%{$search}%")
                ->orWhere('karyawan_id', 'ILIKE', "%{$search}%");
            });
        }

        $result = $data;
        return $result;
    }

    public static function getData($dataFilter, $settings)
    {
        return self::_query($dataFilter)->offset($settings['start'])
            ->limit($settings['limit'])
            ->orderBy($settings['order'], $settings['dir'])
            ->get();
    }

    public static function countData($dataFilter)
    {
        return self::_query($dataFilter)->count();
    }
}
