<?php

namespace App\Models\TugasLuare;

use App\Models\Divisi;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Organisasi;
use Illuminate\Database\Eloquent\Model;
use App\Models\TugasLuare\PengikutTugasLuar;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TugasLuar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tugasluars';
    protected $primaryKey = 'id_tugasluar';
    public $incrementing = false;
    
    protected $fillable = [
        'id_tugasluar',
        'organisasi_id',
        'karyawan_id',
        'ni_karyawan',
        'departemen_id',
        'divisi_id',
        'created_date',
        'tanggal_pergi',
        'tanggal_kembali',
        'jenis_kendaraan',
        'kepemilikan_kendaraan',
        'no_polisi',
        'km_awal',
        'km_akhir',
        'jarak_tempuh',
        'pengemudi_id',
        'tempat_asal',
        'tempat_tujuan',
        'keterangan',
        'pembagi',
        'rate',
        'nominal',
        'millage_id',
        'checked_by',
        'checked_at',
        'legalized_by',
        'legalized_at',
        'known_by',
        'known_at',
        'status'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id', 'id_organisasi');
    }

    public function pengemudi()
    {
        return $this->belongsTo(Karyawan::class, 'pengemudi_id', 'id_karyawan');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id', 'id_departemen');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id', 'id_divisi');
    }

    public function pengikut()
    {
        return $this->hasMany(PengikutTugasLuar::class, 'tugasluar_id', 'id_tugasluar');
    }

    private static function _query($dataFilter)
    {

        $data = self::select(
            'tugasluars.id_tugasluar',
            'tugasluars.organisasi_id',
            'tugasluars.karyawan_id',
            'tugasluars.ni_karyawan',
            'tugasluars.departemen_id',
            'tugasluars.divisi_id',
            'tugasluars.created_date',
            'tugasluars.tanggal_pergi',
            'tugasluars.tanggal_kembali',
            'tugasluars.jenis_kendaraan',
            'tugasluars.kepemilikan_kendaraan',
            'tugasluars.no_polisi',
            'tugasluars.km_awal',
            'tugasluars.km_akhir',
            'tugasluars.jarak_tempuh',
            'tugasluars.pengemudi_id',
            'tugasluars.tempat_asal',
            'tugasluars.tempat_tujuan',
            'tugasluars.keterangan',
            'tugasluars.pembagi',
            'tugasluars.rate',
            'tugasluars.nominal',
            'tugasluars.millage_id',
            'tugasluars.checked_by',
            'tugasluars.checked_at',
            'tugasluars.legalized_by',
            'tugasluars.legalized_at',
            'tugasluars.known_by',
            'tugasluars.known_at',
            'karyawans.nama as nama_karyawan',
        );
        $data->leftJoin('karyawans', 'tugasluars.karyawan_id', 'karyawans.id_karyawan');

        if (isset($dataFilter['organisasi_id'])) {
            $data->where('tugasluars.organisasi_id', $dataFilter['organisasi_id']);
        }

        if (isset($dataFilter['id_karyawan'])) {
            $data->where('tugasluars.karyawan_id', $dataFilter['id_karyawan']);
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('tugasluars.tempat_asal', 'ILIKE', "%{$search}%");
                $query->orWhere('tugasluars.tempat_tujuan', 'ILIKE', "%{$search}%");
                $query->orWhere('karyawans.nama', 'ILIKE', "%{$search}%");
                $query->orWhere('tugasluars.no_polisi', 'ILIKE', "%{$search}%");
                $query->orWhere('tugasluars.jenis_kendaraan', 'ILIKE', "%{$search}%");
                $query->orWhere('tugasluars.keterangan', 'ILIKE', "%{$search}%");
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
