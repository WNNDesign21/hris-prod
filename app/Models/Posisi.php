<?php

namespace App\Models;

use App\Models\Seksi;
use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Organisasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Posisi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'posisis';
    protected $primaryKey = 'id_posisi';

    protected $fillable = [
        'jabatan_id', 'organisasi_id', 'divisi_id', 'departemen_id', 'seksi_id', 'nama', 'parent_id',
    ];

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id', 'id_jabatan');
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

    public function karyawan()
    {
        return $this->belongsToMany(Karyawan::class, 'karyawan_posisi', 'posisi_id', 'karyawan_id');
    }

    private static function _query($dataFilter)
    {

        $getOrg = Organisasi::select("id_organisasi as org_id", "nama as nama_org");
        $getDivisi = Divisi::select("id_divisi as div_id", "nama as nama_div");
        $getDepartemen = Departemen::select("id_departemen as dep_id", "nama as nama_dep");
        $getSeksi = Seksi::select("id_seksi as sek_id", "nama as nama_sek");
        $data = self::select(
            'id_posisi',
            'posisis.jabatan_id',
            'posisis.organisasi_id',
            'posisis.divisi_id',
            'posisis.departemen_id',
            'posisis.seksi_id',
            'posisis.parent_id',
            'posisis.nama as nama_posisi',
            'jabatans.nama as nama_jabatan',
            'org.nama_org as nama_organisasi',
            'div.nama_div as nama_divisi',
            'dep.nama_dep as nama_departemen',
            'sek.nama_sek as nama_seksi',
        )
        ->leftJoin('jabatans', 'posisis.jabatan_id', 'jabatans.id_jabatan')
        ->leftJoinSub($getOrg, 'org', function (JoinClause $joinOrg) {
            $joinOrg->on('posisis.organisasi_id', 'org.org_id');
        })
        ->leftJoinSub($getDivisi, 'div', function (JoinClause $joinDivisi) {
            $joinDivisi->on('posisis.divisi_id', 'div.div_id');
        })
        ->leftJoinSub($getDepartemen, 'dep', function (JoinClause $joinDepartemen) {
            $joinDepartemen->on('posisis.departemen_id', 'dep.dep_id');
        })
        ->leftJoinSub($getSeksi, 'sek', function (JoinClause $joinSeksi) {
            $joinSeksi->on('posisis.seksi_id', 'sek.sek_id');
        });

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('posisis.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('jabatans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('org.nama_org', 'ILIKE', "%{$search}%")
                    ->orWhere('div.nama_div', 'ILIKE', "%{$search}%")
                    ->orWhere('dep.nama_dep', 'ILIKE', "%{$search}%")
                    ->orWhere('sek.nama_sek', 'ILIKE', "%{$search}%");
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

    public function parent()
    {
        return $this->belongsTo(Posisi::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Posisi::class, 'parent_id')->with('children');
    }
}
