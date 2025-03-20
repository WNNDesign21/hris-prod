<?php

namespace App\Models;

use App\Models\Grup;
use App\Models\User;
use App\Models\Piket;
use App\Models\Divisi;
use App\Models\Posisi;
use App\Models\Departemen;
use App\Models\Organisasi;
use App\Models\GrupPattern;
use Illuminate\Support\Facades\DB;
use App\Models\SettingLemburKaryawan;
use App\Models\Attendance\KaryawanGrup;
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
        'ni_karyawan',
        'user_id',
        'organisasi_id',
        'posisi_id',
        'divisi_id',
        'departemen_id',
        'seksi_id',
        'grup_id',
        'no_kk',
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'domisili',
        'email',
        'no_telp',
        'agama',
        'gol_darah',
        'jenis_kelamin',
        'status_keluarga',
        'kategori_keluarga',
        'npwp',
        'no_bpjs_ks',
        'no_bpjs_kt',
        'jenis_kontrak',
        'status_karyawan',
        'sisa_cuti_pribadi',
        'sisa_cuti_bersama',
        'sisa_cuti_tahun_lalu',
        'expired_date_cuti_tahun_lalu',
        'hutang_cuti',
        'tanggal_mulai',
        'tanggal_selesai',
        'no_rekening',
        'nama_bank',
        'nama_rekening',
        'nama_ibu_kandung',
        'jenjang_pendidikan',
        'jurusan_pendidikan',
        'no_telp_darurat',
        'foto',
        'pin',
        'grup_pattern_id'
    ];

    protected $dates = [
        'tanggal_lahir',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    //ACCESSOR / GETTER
    // public function getFotoAttribute($value)
    // {
    //     if ($value) {
    //         return $value;
    //     } else {
    //         return asset('img/no-image.png');
    //     }
    // }

    //SCOPE
    public function scopeAktif($query)
    {
        return $query->where('status_karyawan', 'AT');
    }

    public function scopeOrganisasi($query, $organisasi)
    {
        return $query->where('karyawans.organisasi_id', $organisasi);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function grup()
    {
        return $this->belongsTo(Grup::class, 'grup_id', 'id_grup');
    }

    public function kontrak()
    {
        return $this->hasMany(Kontrak::class, 'karyawan_id', 'id_karyawan');
    }

    public function posisi()
    {
        return $this->belongsToMany(Posisi::class, 'karyawan_posisi', 'karyawan_id', 'posisi_id');
    }

    public function piket()
    {
        return $this->hasMany(Piket::class, 'karyawan_id', 'id_karyawan')
            ->where('organisasi_id', auth()->user()->organisasi_id)
            ->where('expired_date', '>=', now())
            ->exists();
    }

    public function karyawanGrup()
    {
        return $this->hasMany(KaryawanGrup::class, 'karyawan_id', 'id_karyawan');
    }

    public function grupPattern()
    {
        return $this->belongsTo(GrupPattern::class, 'grup_pattern_id', 'id_grup_pattern');
    }

    public function settingLembur()
    {
        return $this->hasOne(SettingLemburKaryawan::class, 'karyawan_id', 'id_karyawan');
    }

    private static function _query($dataFilter)
    {
        $data = self::select(
            'karyawans.id_karyawan',
            'karyawans.ni_karyawan',
            'karyawans.nama',
            'karyawans.email as email_karyawan',
            'karyawans.no_telp as notelp_karyawan',
            'karyawans.jenis_kontrak',
            'karyawans.status_karyawan',
            'grups.nama as nama_grup',
            'users.username',
            'karyawans.tanggal_mulai',
            'karyawans.tanggal_selesai',
            'karyawans.grup_id',
            'karyawans.user_id',
            'karyawans.ni_karyawan',
            'karyawans.tanggal_lahir',
            'karyawans.alamat',
            'karyawans.domisili',
            'karyawans.no_kk',
            'karyawans.nik',
            'karyawans.tempat_lahir',
            'karyawans.agama',
            'karyawans.gol_darah',
            'karyawans.jenis_kelamin',
            'karyawans.status_keluarga',
            'karyawans.kategori_keluarga',
            'karyawans.npwp',
            'karyawans.no_bpjs_ks',
            'karyawans.no_bpjs_kt',
            'karyawans.sisa_cuti_pribadi',
            'karyawans.sisa_cuti_bersama',
            'karyawans.sisa_cuti_tahun_lalu',
            'karyawans.expired_date_cuti_tahun_lalu',
            'karyawans.hutang_cuti',
            'karyawans.no_rekening',
            'karyawans.nama_rekening',
            'karyawans.nama_bank',
            'karyawans.nama_ibu_kandung',
            'karyawans.jenjang_pendidikan',
            'karyawans.jurusan_pendidikan',
            'karyawans.no_telp_darurat',
            'departemens.nama as nama_departemen'
        );

        $organisasi_id = auth()->user()->organisasi_id;
        if ($organisasi_id) {
            $data->where('karyawans.organisasi_id', $organisasi_id);
        }

        if(isset($dataFilter['departemen'])) {
            $data->where('departemens.id_departemen', $dataFilter['departemen']);
        }

        if(isset($dataFilter['departemens'])) {
            $data->whereIn('departemens.id_departemen', $dataFilter['departemens']);
        }

        if(isset($dataFilter['grup'])) {
            $data->where('grups.id_grup', $dataFilter['grup']);
        }

        if(isset($dataFilter['jenisKontrak'])) {
            $data->where('karyawans.jenis_kontrak', $dataFilter['jenisKontrak']);
        }

        if(isset($dataFilter['statusKaryawan'])) {
            $data->where('karyawans.status_karyawan', $dataFilter['statusKaryawan']);
        }

        if(isset($dataFilter['jenisKelamin'])) {
            $data->where('karyawans.jenis_kelamin', $dataFilter['jenisKelamin']);
        }

        if(isset($dataFilter['agama'])) {
            $data->where('karyawans.agama', $dataFilter['agama']);
        }

        if(isset($dataFilter['golonganDarah'])) {
            $data->where('karyawans.gol_darah', $dataFilter['golonganDarah']);
        }

        if(isset($dataFilter['statusKeluarga'])) {
            $data->where('karyawans.status_keluarga', $dataFilter['statusKeluarga']);
        }

        if(isset($dataFilter['kategoriKeluarga'])) {
            $data->where('karyawans.kategori_keluarga', $dataFilter['kategoriKeluarga']);
        }

        if(isset($dataFilter['namaBank'])) {
            $data->where('karyawans.nama_bank', $dataFilter['namaBank']);
        }

        if(isset($dataFilter['nama'])) {
            $data->where('karyawans.nama', 'ILIKE', '%'.$dataFilter['nama'].'%');
        }

        if(isset($dataFilter['nik'])) {
            $data->where('karyawans.ni_karyawan', $dataFilter['nik']);
        }

        $data->leftJoin('users', 'karyawans.user_id', 'users.id')
        ->leftJoin('grups', 'karyawans.grup_id', 'grups.id_grup')
        ->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
        ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
        ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')
        ->groupBy(
            'karyawans.id_karyawan',
            'karyawans.ni_karyawan',
            'karyawans.nama',
            'karyawans.email',
            'karyawans.no_telp',
            'karyawans.jenis_kontrak',
            'karyawans.status_karyawan',
            'grups.nama',
            'users.username',
            'karyawans.tanggal_mulai',
            'karyawans.tanggal_selesai',
            'karyawans.grup_id',
            'karyawans.user_id',
            'karyawans.ni_karyawan',
            'karyawans.tanggal_lahir',
            'karyawans.alamat',
            'karyawans.domisili',
            'karyawans.no_kk',
            'karyawans.nik',
            'karyawans.tempat_lahir',
            'karyawans.agama',
            'karyawans.gol_darah',
            'karyawans.jenis_kelamin',
            'karyawans.status_keluarga',
            'karyawans.kategori_keluarga',
            'karyawans.npwp',
            'karyawans.no_bpjs_ks',
            'karyawans.no_bpjs_kt',
            'karyawans.sisa_cuti_pribadi',
            'karyawans.sisa_cuti_bersama',
            'karyawans.sisa_cuti_tahun_lalu',
            'karyawans.expired_date_cuti_tahun_lalu',
            'karyawans.hutang_cuti',
            'karyawans.no_rekening',
            'karyawans.nama_rekening',
            'karyawans.nama_bank',
            'karyawans.nama_ibu_kandung',
            'karyawans.jenjang_pendidikan',
            'karyawans.jurusan_pendidikan',
            'karyawans.no_telp_darurat',
            'departemens.nama'
        );

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('karyawans.id_karyawan', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.email', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.no_telp', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.jenis_kontrak', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.status_karyawan', 'ILIKE', "%{$search}%")
                    ->orWhere('grups.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('users.username', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.tanggal_mulai', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.ni_karyawan', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.tanggal_selesai', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.alamat', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.domisili', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.no_kk', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nik', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.tempat_lahir', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.agama', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.gol_darah', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.jenis_kelamin', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.status_keluarga', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.kategori_keluarga', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.npwp', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.no_bpjs_ks', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.no_bpjs_kt', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.hutang_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.no_rekening', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama_rekening', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama_bank', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama_ibu_kandung', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.jenjang_pendidikan', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.jurusan_pendidikan', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.no_telp_darurat', 'ILIKE', "%{$search}%")
                    ->orWhere('departemens.nama', 'ILIKE', "%{$search}%");
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

    public static function getDepartemenMember($departemen_id)
    {
        return self::select('karyawans.id_karyawan', 'karyawans.nama', 'karyawans.ni_karyawan')
            ->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
            ->where('posisis.departemen_id', $departemen_id)
            ->get();
    }

    private static function _shiftGroup($dataFilter)
    {
        $data = self::select(
            'karyawans.id_karyawan',
            'karyawans.ni_karyawan',
            'karyawans.nama',
            'departemens.nama as departemen',
            'grups.nama as grup',
            'karyawans.grup_id',
            'karyawans.pin',
            'divisis.nama as divisi',
            'grups.jam_masuk',
            'grups.jam_keluar',
            'grup_patterns.nama as grup_pattern',
            'karyawans.grup_pattern_id',
        );

        $data->leftJoin('grups', 'karyawans.grup_id', 'grups.id_grup')
        ->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
        ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
        ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')
        ->leftJoin('grup_patterns', 'karyawans.grup_pattern_id', 'grup_patterns.id_grup_pattern')
        ->leftJoin('divisis', 'departemens.divisi_id', 'divisis.id_divisi');

        $organisasi_id = auth()->user()->organisasi_id;
        if ($organisasi_id) {
            $data->where('karyawans.organisasi_id', $organisasi_id);
        }

        if(isset($dataFilter['departemen'])) {
            $data->where('departemens.id_departemen', $dataFilter['departemen']);
        }

        if(isset($dataFilter['grup'])) {
            $data->where('grups.id_grup', $dataFilter['grup']);
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('karyawans.id_karyawan', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.pin', 'ILIKE', "%{$search}%")
                    ->orWhere('grups.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('grup_patterns.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('departemens.nama', 'ILIKE', "%{$search}%");
            });
        }

        $result = $data;
        return $result;
    }

    public static function getDataShiftgroup($dataFilter, $settings)
    {
        return self::_shiftGroup($dataFilter)->offset($settings['start'])
            ->limit($settings['limit'])
            ->orderBy($settings['order'], $settings['dir'])
            ->get();
    }

    public static function countDataShiftgroup($dataFilter)
    {
        return self::_shiftGroup($dataFilter)->get()->count();
    }

    private static function _ksk($dataFilter)
    {
        $subQuery = DB::table('karyawan_posisi')
            ->select('karyawan_id', 'posisi_id')
            ->whereNull('deleted_at')
            ->distinct();

        $data = self::select(
            'posisis.jabatan_id',
            'posisis.id_posisi',
            'jabatans.nama as jabatan_nama',
            'departemens.id_departemen',
            'divisis.id_divisi',
            'departemens.nama as departemen_nama',
            'divisis.nama as divisi_nama',
            DB::raw('COUNT(CASE WHEN EXTRACT(MONTH FROM tanggal_selesai) = EXTRACT(MONTH FROM NOW() + INTERVAL \'1 month\') AND EXTRACT(YEAR FROM tanggal_selesai) = EXTRACT(YEAR FROM NOW() + INTERVAL \'1 month\') THEN 1 END) as jumlah_karyawan_habis')
        )
        ->joinSub($subQuery, 'distinct_karyawan_posisi', function ($join) {
            $join->on('karyawans.id_karyawan', 'distinct_karyawan_posisi.karyawan_id');
        })
        ->leftJoin('posisis', 'distinct_karyawan_posisi.posisi_id', 'posisis.id_posisi')
        ->leftJoin('jabatans', 'posisis.jabatan_id', 'jabatans.id_jabatan')
        ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')
        ->leftJoin('divisis', 'departemens.divisi_id', 'divisis.id_divisi')
        ->whereMonth('tanggal_selesai', now()->addMonth()->month)
        ->where('karyawans.status_karyawan', 'AT')
        ->where('karyawans.organisasi_id', auth()->user()->organisasi_id);

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
            $query->where('departemens.nama', 'LIKE', "%{$search}%")
                ->orWhere('divisis.nama', 'LIKE', "%{$search}%");
            });
        }

        $data->groupBy('departemens.nama', 'divisis.nama', 'departemens.id_departemen', 'divisis.id_divisi', 'posisis.jabatan_id', 'jabatans.nama', 'posisis.id_posisi');
        return $data;
    }

    public static function getDataKSK($dataFilter, $settings)
    {
        return self::_ksk($dataFilter)->offset($settings['start'])
            ->limit($settings['limit'])
            ->orderBy($settings['order'], $settings['dir'])
            ->get();
    }

    public static function countDataKSK($dataFilter)
    {
        return self::_ksk($dataFilter)->get()->count();
    }
}
