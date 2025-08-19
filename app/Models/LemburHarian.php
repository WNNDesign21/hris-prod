<?php

namespace App\Models;

use App\Models\Divisi;
use App\Models\Departemen;
use App\Models\Organisasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LemburHarian extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lembur_harians';
    protected $primaryKey = 'id_lembur_harian';

    protected $fillable = [
        'organisasi_id',
        'departemen_id',
        'divisi_id',
        'total_durasi_lembur',
        'total_nominal_lembur',
        'tanggal_lembur',
    ];

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id', 'id_organisasi');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id', 'id_departemen');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id', 'id_divisi');
    }

    public static function getMonthlyLemburPerDepartemen($dataFilter)
    {
        $organisasi_id = auth()->user()->organisasi_id;
        $data = self::selectRaw(
            '
            SUM(CASE WHEN EXTRACT(MONTH FROM lembur_harians.tanggal_lembur) = 1 THEN lembur_harians.total_nominal_lembur ELSE 0 END) as januari,
            SUM(CASE WHEN EXTRACT(MONTH FROM lembur_harians.tanggal_lembur) = 2 THEN lembur_harians.total_nominal_lembur ELSE 0 END) as februari,
            SUM(CASE WHEN EXTRACT(MONTH FROM lembur_harians.tanggal_lembur) = 3 THEN lembur_harians.total_nominal_lembur ELSE 0 END) as maret,
            SUM(CASE WHEN EXTRACT(MONTH FROM lembur_harians.tanggal_lembur) = 4 THEN lembur_harians.total_nominal_lembur ELSE 0 END) as april,
            SUM(CASE WHEN EXTRACT(MONTH FROM lembur_harians.tanggal_lembur) = 5 THEN lembur_harians.total_nominal_lembur ELSE 0 END) as mei,
            SUM(CASE WHEN EXTRACT(MONTH FROM lembur_harians.tanggal_lembur) = 6 THEN lembur_harians.total_nominal_lembur ELSE 0 END) as juni,
            SUM(CASE WHEN EXTRACT(MONTH FROM lembur_harians.tanggal_lembur) = 7 THEN lembur_harians.total_nominal_lembur ELSE 0 END) as juli,
            SUM(CASE WHEN EXTRACT(MONTH FROM lembur_harians.tanggal_lembur) = 8 THEN lembur_harians.total_nominal_lembur ELSE 0 END) as agustus,
            SUM(CASE WHEN EXTRACT(MONTH FROM lembur_harians.tanggal_lembur) = 9 THEN lembur_harians.total_nominal_lembur ELSE 0 END) as september,
            SUM(CASE WHEN EXTRACT(MONTH FROM lembur_harians.tanggal_lembur) = 10 THEN lembur_harians.total_nominal_lembur ELSE 0 END) as oktober,
            SUM(CASE WHEN EXTRACT(MONTH FROM lembur_harians.tanggal_lembur) = 11 THEN lembur_harians.total_nominal_lembur ELSE 0 END) as november,
            SUM(CASE WHEN EXTRACT(MONTH FROM lembur_harians.tanggal_lembur) = 12 THEN lembur_harians.total_nominal_lembur ELSE 0 END) as desember'
        )
        ->leftJoin('departemens', 'lembur_harians.departemen_id', 'departemens.id_departemen');

        if(auth()->user()->hasAnyRole(['personalia', 'personalia-lembur']) || (auth()->user()->karyawan && (auth()->user()->karyawan->posisi[0]->jabatan_id == 2 && auth()->user()->karyawan->posisi[0]->organisasi_id) || auth()->user()->karyawan->posisi[0]->jabatan_id == 5)){
            $data->where('lembur_harians.organisasi_id', $organisasi_id);
        }

        if(isset($dataFilter['tahun'])){
            $data->whereYear('lembur_harians.tanggal_lembur', $dataFilter['tahun']);
        } else {
            $data->whereYear('lembur_harians.tanggal_lembur', date('Y'));
        }

        if(isset($dataFilter['departemen'])){
            $data->whereIn('lembur_harians.departemen_id', $dataFilter['departemen']);
        }

        if(auth()->user()->hasRole('atasan') && (auth()->user()->karyawan && (auth()->user()->karyawan->posisi[0]->jabatan_id == 3 || auth()->user()->karyawan->posisi[0]->jabatan_id == 4))) {
            $posisi = auth()->user()->karyawan->posisi;
            if($posisi[0]->jabatan_id == 3){
                foreach($posisi as $p){
                    if ($p->departemen_id !== null) {
                        $departemen_id[] = $p->departemen_id;
                    }
                }
                $data->whereIn('lembur_harians.departemen_id', $departemen_id);
            } else {
                $data->where('lembur_harians.departemen_id', auth()->user()->karyawan->posisi[0]->departemen_id);
            }
        }
        
        $data->whereNotNull('departemens.nama');
        // ->groupBy('departemens.nama','departemens.id_departemen');

        return $data->first();
    }

    public static function getWeeklyLemburPerDepartemen($dataFilter)
    {
        $organisasi_id = auth()->user()->organisasi_id;
        $data = self::selectRaw(
            'departemens.nama as departemen, 
            SUM(CASE WHEN EXTRACT(DAY FROM lembur_harians.tanggal_lembur) BETWEEN 1 AND 7 THEN lembur_harians.total_nominal_lembur ELSE 0 END) as minggu_1,
            SUM(CASE WHEN EXTRACT(DAY FROM lembur_harians.tanggal_lembur) BETWEEN 8 AND 15 THEN lembur_harians.total_nominal_lembur ELSE 0 END) as minggu_2,
            SUM(CASE WHEN EXTRACT(DAY FROM lembur_harians.tanggal_lembur) BETWEEN 16 AND 23 THEN lembur_harians.total_nominal_lembur ELSE 0 END) as minggu_3,
            SUM(CASE WHEN EXTRACT(DAY FROM lembur_harians.tanggal_lembur) BETWEEN 24 AND 31 THEN lembur_harians.total_nominal_lembur ELSE 0 END) as minggu_4'
        )
        ->leftJoin('departemens', 'lembur_harians.departemen_id', 'departemens.id_departemen');
        if(auth()->user()->hasAnyRole(['personalia', 'personalia-lembur']) || (auth()->user()->karyawan && (auth()->user()->karyawan->posisi[0]->jabatan_id == 2 && auth()->user()->karyawan->posisi[0]->organisasi_id) || auth()->user()->karyawan->posisi[0]->jabatan_id == 5)){
            $data->where('lembur_harians.organisasi_id', $organisasi_id);
        }

        if(isset($dataFilter['year'])){
            $data->whereYear('lembur_harians.tanggal_lembur', $dataFilter['year']);
        } else {
            $data->whereYear('lembur_harians.tanggal_lembur', date('Y'));
        }

        if(isset($dataFilter['month'])){
            $data->whereMonth('lembur_harians.tanggal_lembur', $dataFilter['month']);
        } else {
            $data->whereMonth('lembur_harians.tanggal_lembur', date('m'));
        }

        if(isset($dataFilter['departemen'])){
            $data->whereIn('lembur_harians.departemen_id', $dataFilter['departemen']);
        }

        if(auth()->user()->hasRole('atasan') && (auth()->user()->karyawan && (auth()->user()->karyawan->posisi[0]->jabatan_id == 3 || auth()->user()->karyawan->posisi[0]->jabatan_id == 4))) {
            $posisi = auth()->user()->karyawan->posisi;
            if($posisi[0]->jabatan_id == 3){
                foreach($posisi as $p){
                    if ($p->departemen_id !== null) {
                        $departemen_id[] = $p->departemen_id;
                    }
                }
                $data->whereIn('lembur_harians.departemen_id', $departemen_id);
            } else {
                $data->where('lembur_harians.departemen_id', auth()->user()->karyawan->posisi[0]->departemen_id);
            }
        }

        $data->whereNotNull('departemens.nama')->groupBy('departemens.nama');

        return $data->get();
    }

    public static function getCurrentMonthLemburPerDepartemen($dataFilter)
    {
        $organisasi_id = auth()->user()->organisasi_id;
        $data = self::selectRaw(
            'departemens.nama as departemen, 
            departemens.id_departemen,
            SUM(lembur_harians.total_nominal_lembur) as total_nominal,
            SUM(lembur_harians.total_durasi_lembur) as total_durasi'
        )
        ->leftJoin('departemens', 'lembur_harians.departemen_id', 'departemens.id_departemen');
        if(auth()->user()->hasAnyRole(['personalia', 'personalia-lembur']) || (auth()->user()->karyawan && (auth()->user()->karyawan->posisi[0]->jabatan_id == 2 && auth()->user()->karyawan->posisi[0]->organisasi_id) || auth()->user()->karyawan->posisi[0]->jabatan_id == 5)){
            $data->where('lembur_harians.organisasi_id', $organisasi_id);
        }

        if(isset($dataFilter['year'])){
            $data->whereYear('lembur_harians.tanggal_lembur', $dataFilter['year']);
        } else {
            $data->whereYear('lembur_harians.tanggal_lembur', date('Y'));
        }

        if(isset($dataFilter['month'])){
            $data->whereMonth('lembur_harians.tanggal_lembur', $dataFilter['month']);
        } else {
            $data->whereMonth('lembur_harians.tanggal_lembur', date('m'));
        }

        if(isset($dataFilter['departemen'])){
            $data->whereIn('lembur_harians.departemen_id', $dataFilter['departemen']);
        }

        if(auth()->user()->hasRole('atasan') && (auth()->user()->karyawan && (auth()->user()->karyawan->posisi[0]->jabatan_id == 3 || auth()->user()->karyawan->posisi[0]->jabatan_id == 4))) {
            $posisi = auth()->user()->karyawan->posisi;
            if($posisi[0]->jabatan_id == 3){
                foreach($posisi as $p){
                    if ($p->departemen_id !== null) {
                        $departemen_id[] = $p->departemen_id;
                    }
                }
                $data->whereIn('lembur_harians.departemen_id', $departemen_id);
            } else {
                $data->where('lembur_harians.departemen_id', auth()->user()->karyawan->posisi[0]->departemen_id);
            }
        }

        $data->whereNotNull('departemens.nama')->groupBy('departemens.nama','departemens.id_departemen');

        return $data->get();
    }
}
