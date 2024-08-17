<?php

namespace App\Models;

use App\Models\Grup;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Posisi;
use App\Models\Departemen;
use App\Models\Organisasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Karyawan extends Model
{
    use HasFactory, SoftDeletes;

    
    protected $table = 'karyawans';
    
    protected $primaryKey = 'id_karyawan';
    public $incrementing = false;

    protected $fillable = [
        'id_karyawan',
        'user_id',
        'organisasi_id',
        'posisi_id',
        'divisi_id',
        'departemen_id',
        'seksi_id',
        'grup_id',
        'no_ktp',
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'email',
        'no_telp',
        'gol_darah',
        'jenis_kelamin',
        'status_keluarga',
        'npwp',
        'no_bpjs_ks',
        'no_bpjs_kt',
        'jenis_kontrak',
        'status_karyawan',
        'sisa_cuti',
        'tahun_masuk',
        'tahun_keluar',
    ];
    
    protected $dates = [
        'tanggal_lahir',
        'tahun_masuk',
        'tahun_keluar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function grup()
    {
        return $this->belongsTo(Grup::class, 'grup_id', 'id_grup');
    }

    public function posisi()
    {
        return $this->belongsToMany(Posisi::class, 'karyawan_posisi', 'karyawan_id', 'posisi_id');
    }

    private static function _query($dataFilter)
    {

        $data = self::select(
            'id_karyawan',
            'karyawans.nama',
            'karyawans.email as email_karyawan',
            'karyawans.no_telp as notelp_karyawan',
            'jenis_kontrak',
            'status_karyawan',
            'grups.nama as nama_grup',
            'users.username',
            'grup_id',
            'user_id',
        )
        ->leftJoin('users', 'karyawans.user_id', 'users.id')
        ->leftJoin('grups', 'karyawans.grup_id', 'grups.id_grup');
        

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('id_karyawan', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.email', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.no_telp', 'ILIKE', "%{$search}%")
                    ->orWhere('jenis_kontrak', 'ILIKE', "%{$search}%")
                    ->orWhere('status_karyawan', 'ILIKE', "%{$search}%")
                    ->orWhere('grups.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('users.username', 'ILIKE', "%{$search}%");
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
