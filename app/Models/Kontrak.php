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
        'organisasi_id',
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
        'tanggal_mulai_before',
        'tanggal_selesai_before',
        'isReactive',
        // 'status_change_by',
        // 'status_change_date',
        'attachment',
        'evidence'
    ];
    
    protected $dates = [
        'tanggal_mulai',
        'tanggal_selesai',
        'issued_date',
        // 'status_change_date'
    ];

    public function scopeOrganisasi($query, $organisasi)
    {
        return $query->where('kontraks.organisasi_id', $organisasi);
    }

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
            'kontraks.id_kontrak',
            'kontraks.karyawan_id',
            'kontraks.posisi_id',
            'posisis.nama as nama_posisis',
            'kontraks.nama_posisi',
            'kontraks.no_surat',
            'kontraks.tempat_administrasi',
            'kontraks.issued_date',
            'kontraks.durasi',
            'kontraks.salary',
            'kontraks.deskripsi',
            'kontraks.tanggal_mulai as tanggal_mulai_kontrak',
            'kontraks.tanggal_selesai as tanggal_selesai_kontrak',
            'kontraks.jenis',
            'kontraks.status',
            'kontraks.attachment',
            'kontraks.evidence',
            'kontraks.isReactive',
            'karyawans.nama as nama_karyawan',
            'departemens.nama as nama_departemen',
            'kontraks.created_at'
        )
        ->leftJoin('karyawans', 'kontraks.karyawan_id', 'karyawans.id_karyawan')
        ->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
        ->leftJoin('posisis', 'kontraks.posisi_id', 'posisis.id_posisi')
        ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')
        ->groupBy(
            'kontraks.id_kontrak',
            'kontraks.karyawan_id',
            'kontraks.posisi_id',
            'kontraks.nama_posisi',
            'kontraks.no_surat',
            'kontraks.tempat_administrasi',
            'kontraks.issued_date',
            'kontraks.durasi',
            'kontraks.salary',
            'kontraks.deskripsi',
            'kontraks.tanggal_mulai',
            'kontraks.tanggal_selesai',
            'kontraks.jenis',
            'kontraks.status',
            'kontraks.attachment',
            'kontraks.evidence',
            'kontraks.isReactive',
            'karyawans.nama',
            'departemens.nama',
            'posisis.nama'
        );

        $organisasi_id = auth()->user()->organisasi_id;
        if ($organisasi_id) {
            $data->where('kontraks.organisasi_id', $organisasi_id);
        }

        if(isset($dataFilter['departemen'])) {
            $data->where('departemens.id_departemen', $dataFilter['departemen']);
        }

        if(isset($dataFilter['noSurat'])) {
            $data->where('kontraks.no_surat', $dataFilter['noSurat']);
        }

        if(isset($dataFilter['nama'])) {
            $data->where('karyawans.nama', $dataFilter['nama']);
        }

        if(isset($dataFilter['jenisKontrak'])) {
            $data->where('kontraks.jenis', $dataFilter['jenisKontrak']);
        }

        if(isset($dataFilter['statusKontrak'])) {
            $data->where('kontraks.status', $dataFilter['statusKontrak']);
        }

        if(isset($dataFilter['tanggalMulaistart'])) {
            $data->whereDate('kontraks.tanggal_mulai', '>=' ,$dataFilter['tanggalMulaistart']);
        }

        if(isset($dataFilter['tanggalMulaiend'])) {
            $data->whereDate('kontraks.tanggal_mulai', '<=' ,$dataFilter['tanggalMulaiend']);
        }

        if(isset($dataFilter['namaPosisi'])) {
            $data->where('kontraks.nama_posisi', $dataFilter['namaPosisi']);
        }

        if(isset($dataFilter['attachment'])) {
            $data->whereNotNull('kontraks.attachment');
        }

        if(isset($dataFilter['evidence'])) {
            $data->whereNotNull('kontraks.evidence');
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('karyawans.nama', 'ILIKE', "%{$search}%")
                ->orWhere('kontraks.nama_posisi', 'ILIKE', "%{$search}%")
                ->orWhere('kontraks.durasi', 'ILIKE', "%{$search}%")
                ->orWhere('kontraks.status', 'ILIKE', "%{$search}%")
                ->orWhere('kontraks.salary', 'ILIKE', "%{$search}%")
                ->orWhere('kontraks.tanggal_mulai', 'ILIKE', "%{$search}%")
                ->orWhere('kontraks.tanggal_selesai', 'ILIKE', "%{$search}%")
                ->orWhere('kontraks.issued_date', 'ILIKE', "%{$search}%")
                ->orWhere('kontraks.id_kontrak', 'ILIKE', "%{$search}%")
                ->orWhere('kontraks.karyawan_id', 'ILIKE', "%{$search}%")
                ->orWhere('departemens.nama', 'ILIKE', "%{$search}%");
            });
        }

        $data->orderBy('kontraks.created_at', 'DESC');

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
