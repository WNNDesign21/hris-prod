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

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id', 'id_organisasi');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id', 'id_divisi');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id', 'id_departemen');
    }

    public function seksi()
    {
        return $this->belongsTo(Seksi::class, 'seksi_id', 'id_seksi');
    }

    public function posisi()
    {
        return $this->belongsToMany(Posisi::class, 'karyawan_posisi', 'karyawan_id', 'posisi_id');
    }

    private static function _query($dataFilter)
    {

        $getGrup = Grup::select("id_grup as gp_id", "nama as nama_gp");
        $data = self::select(
            'id_karyawan',
            'karyawans.nama as nama_karyawan',
            'users.username as username_karyawan',
            'karyawans.user_id as user_id',
            'karyawans.email as email_karyawan',
            'karyawans.no_telp as no_telp_karyawan',
            'karyawans.jenis_kontrak as jenis_kontrak',
            'karyawans.status_karyawan as status_karyawan',
            'gp.nama_gp as nama_grup',
            'karyawans.grup_id',
        )
        ->leftJoin('users', 'karyawans.user_id', 'users.id')
        ->leftJoinSub($getGrup, 'gp', function (JoinClause $joinGrup) {
            $joinGrup->on('karyawans.grup_id', 'gp.gp_id');
        });

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('users.username', 'ILIKE', "%{$search}%")
                    ->orWhere('id_karyawan', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.email', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.no_telp', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.jenis_kontrak', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.status_karyawan', 'ILIKE', "%{$search}%")
                    ->orWhere('gp.nama_gp', 'ILIKE', "%{$search}%");
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
