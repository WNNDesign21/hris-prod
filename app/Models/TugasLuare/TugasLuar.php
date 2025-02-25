<?php

namespace App\Models\TugasLuare;

use App\Models\Divisi;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Organisasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use App\Models\TugasLuare\DetailTugasLuar;
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
        'tanggal',
        'tanggal_pergi_planning',
        'tanggal_kembali_planning',
        'tanggal_pergi_aktual',
        'tanggal_kembali_aktual',
        'jenis_kendaraan',
        'jenis_kepemilikan',
        'jenis_keberangkatan',
        'no_polisi',
        'km_awal',
        'km_akhir',
        'km_selisih',
        'km_standar',
        'pengemudi_id',
        'tempat_asal',
        'tempat_tujuan',
        'keterangan',
        'pembagi',
        'bbm',
        'rate',
        'nominal',
        'millage_id',
        'status',
        'checked_by',
        'checked_at',
        'legalized_by',
        'legalized_at',
        'rejected_by',
        'rejected_at',
        'rejected_note',
        'last_changed_by',
        'last_changed_at'
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
        return $this->hasMany(DetailTugasLuar::class, 'tugasluar_id', 'id_tugasluar');
    }

    private static function _query($dataFilter)
    {

        $getPengemudi = Karyawan::select("id_karyawan as id_pengemudi", "nama as nama_pengemudi");
        $data = self::select(
            'tugasluars.id_tugasluar',
            'tugasluars.organisasi_id',
            'tugasluars.karyawan_id',
            'tugasluars.ni_karyawan',
            'tugasluars.departemen_id',
            'tugasluars.divisi_id',
            'tugasluars.tanggal',
            'tugasluars.tanggal_pergi_planning',
            'tugasluars.tanggal_kembali_planning',
            'tugasluars.tanggal_pergi_aktual',
            'tugasluars.tanggal_kembali_aktual',
            'tugasluars.jenis_kendaraan',
            'tugasluars.jenis_kepemilikan',
            'tugasluars.jenis_keberangkatan',
            'tugasluars.no_polisi',
            'tugasluars.km_awal',
            'tugasluars.km_akhir',
            'tugasluars.km_selisih',
            'tugasluars.km_standar',
            'tugasluars.pengemudi_id',
            'tugasluars.tempat_asal',
            'tugasluars.tempat_tujuan',
            'tugasluars.keterangan',
            'tugasluars.pembagi',
            'tugasluars.bbm',
            'tugasluars.rate',
            'tugasluars.nominal',
            'tugasluars.millage_id',
            'tugasluars.status',
            'tugasluars.checked_by',
            'tugasluars.checked_at',
            'tugasluars.legalized_by',
            'tugasluars.legalized_at',
            'tugasluars.rejected_by',
            'tugasluars.rejected_at',
            'tugasluars.rejected_note',
            'tugasluars.last_changed_by',
            'tugasluars.last_changed_at',
            'karyawans.nama as karyawan',
            'departemens.nama as departemen',
            'divisis.nama as divisi',
            'p.nama_pengemudi',
        );
        $data->leftJoin('karyawans', 'tugasluars.karyawan_id', 'karyawans.id_karyawan')
        ->leftJoinSub($getPengemudi, 'p', function (JoinClause $joinPengemudi) {
            $joinPengemudi->on('tugasluars.pengemudi_id', 'p.id_pengemudi');
        })
        ->leftJoin('karyawan_posisi', 'tugasluars.karyawan_id', 'karyawan_posisi.karyawan_id')
        ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
        ->leftJoin('departemens', 'tugasluars.departemen_id', 'departemens.id_departemen')
        ->leftJoin('divisis', 'tugasluars.divisi_id', 'divisis.id_divisi');

        if (isset($dataFilter['organisasi_id'])) {
            $data->where('tugasluars.organisasi_id', $dataFilter['organisasi_id']);
        }

        if (isset($dataFilter['id_karyawan'])) {
            $data->where('tugasluars.karyawan_id', $dataFilter['id_karyawan']);
        }

        if (isset($dataFilter['member_posisi_id'])) {
            $data->whereIn('posisis.id_posisi', $dataFilter['member_posisi_id']);
        }

        if (isset($dataFilter['nopol'])) {
            $data->where('tugasluars.no_polisi', 'ILIKE', "%{$dataFilter['nopol']}%");
        }

        if (isset($dataFilter['departemen_id'])) {
            $data->whereIn('tugasluars.departemen_id', $dataFilter['departemen_id']);
        }

        if (isset($dataFilter['from']) && isset($dataFilter['to'])) {
            $data->whereBetween('tugasluars.tanggal', [$dataFilter['from'], $dataFilter['to']]);
        }

        if (isset($dataFilter['status'])) {
            $status = $dataFilter['status'];
            $data->where('tugasluars.status', $status);
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

        if (auth()->user()->hasRole('personalia')) {
            $data->orderByRaw("CASE 
                WHEN tugasluars.status = 'WAITING' AND tugasluars.checked_by IS NOT NULL AND tugasluars.legalized_by IS NULL AND tugasluars.rejected_by IS NULL THEN 1
                WHEN tugasluars.rejected_by IS NOT NULL THEN 3
                ELSE 2
            END, tugasluars.tanggal DESC");
        }

        if (auth()->user()->hasRole('atasan')) {
            if (!isset($dataFilter['id_karyawan'])) {
                $data->orderByRaw("CASE 
                    WHEN tugasluars.status = 'WAITING' AND tugasluars.checked_by IS NULL AND tugasluars.rejected_by IS NULL THEN 1
                    WHEN tugasluars.rejected_by IS NOT NULL THEN 3
                    ELSE 2
                END, tugasluars.tanggal DESC");
            }
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
