<?php

namespace App\Models\KSK;

use App\Models\Divisi;
use App\Models\Posisi;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Organisasi;
use App\Models\KSK\CleareanceDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cleareance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cleareances';
    protected $primaryKey = 'id_cleareance';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_cleareance',
        'karyawan_id',
        'organisasi_id',
        'divisi_id',
        'departemen_id',
        'jabatan_id',
        'posisi_id',
        'nama_divisi',
        'nama_departemen',
        'nama_jabatan',
        'nama_posisi',
        'tanggal_akhir_bekerja',
        'status',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
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

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id', 'id_jabatan');
    }

    public function posisi()
    {
        return $this->belongsTo(Posisi::class, 'posisi_id', 'id_posisi');
    }

    public function cleareance()
    {
        return $this->hasMany(CleareanceDetail::class, 'cleareance_id', 'id_cleareance');
    }

    private static function _query($dataFilter)
    {

        $data = self::select(
            'cleareances.*',
        );
        $data->leftJoin('karyawans', 'karyawans.id_karyawan', 'cleareances.karyawan_id');

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('cleareances.id_cleareance', 'ILIKE', "%{$search}%")
                    ->orWhere('cleareances.karyawan_id', 'ILIKE', "%{$search}%")
                    ->orWhere('cleareances.nama_divisi', 'ILIKE', "%{$search}%")
                    ->orWhere('cleareances.nama_departemen', 'ILIKE', "%{$search}%")
                    ->orWhere('cleareances.nama_jabatan', 'ILIKE', "%{$search}%")
                    ->orWhere('cleareances.nama_posisi', 'ILIKE', "%{$search}%")
                    ->orWhere('cleareances.tanggal_akhir_bekerja', 'ILIKE', "%{$search}%")
                    ->orWhere('cleareances.status', 'ILIKE', "%{$search}%");
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
        return self::_query($dataFilter)->get()->count();
    }
}
