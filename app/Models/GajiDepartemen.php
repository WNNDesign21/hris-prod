<?php

namespace App\Models;

use App\Models\Departemen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GajiDepartemen extends Model
{
    use HasFactory;

    protected $table = 'gaji_departemens';
    protected $primaryKey = 'id_gaji_departemen';

    protected $fillable = [
        'departemen_id','periode','total_gaji','nominal_batas_lembur', 'organisasi_id'
    ];

    public function departemen()
    {
        $this->belongsTo(Departemen::class, 'departemen_id', 'id_departemen');
    }

    private static function _query($dataFilter)
    {
        $data = self::select(
            'gaji_departemens.id_gaji_departemen',
            'gaji_departemens.departemen_id',
            'gaji_departemens.organisasi_id',
            'gaji_departemens.periode',
            'gaji_departemens.total_gaji',
            'gaji_departemens.nominal_batas_lembur',
            'departemens.nama as nama_departemen',
        );

        $data->leftJoin('departemens', 'gaji_departemens.departemen_id', 'departemens.id_departemen');

        $organisasi_id = auth()->user()->organisasi_id;

        $data->where('gaji_departemens.organisasi_id', $organisasi_id);

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('departemens.nama', 'ILIKE', '%' . $search . '%')
                    ->orWhere('gaji_departemens.periode', 'ILIKE', '%' . $search . '%')
                    ->orWhere('gaji_departemens.total_gaji', 'ILIKE', '%' . $search . '%')
                    ->orWhere('gaji_departemens.nominal_batas_lembur', 'ILIKE', '%' . $search . '%');
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

    public static function getMonthlyNominalBatasAllDepartemen()
    {
        $organisasi_id = auth()->user()->organisasi_id;
        $year = date('Y');
        $month = date('m');
        $data = self::selectRaw(
            'SUM(CASE WHEN EXTRACT(MONTH FROM gaji_departemens.periode) = 1 THEN gaji_departemens.nominal_batas_lembur ELSE 0 END) as januari,
            SUM(CASE WHEN EXTRACT(MONTH FROM gaji_departemens.periode) = 2 THEN gaji_departemens.nominal_batas_lembur ELSE 0 END) as februari,
            SUM(CASE WHEN EXTRACT(MONTH FROM gaji_departemens.periode) = 3 THEN gaji_departemens.nominal_batas_lembur ELSE 0 END) as maret,
            SUM(CASE WHEN EXTRACT(MONTH FROM gaji_departemens.periode) = 4 THEN gaji_departemens.nominal_batas_lembur ELSE 0 END) as april,
            SUM(CASE WHEN EXTRACT(MONTH FROM gaji_departemens.periode) = 5 THEN gaji_departemens.nominal_batas_lembur ELSE 0 END) as mei,
            SUM(CASE WHEN EXTRACT(MONTH FROM gaji_departemens.periode) = 6 THEN gaji_departemens.nominal_batas_lembur ELSE 0 END) as juni,
            SUM(CASE WHEN EXTRACT(MONTH FROM gaji_departemens.periode) = 7 THEN gaji_departemens.nominal_batas_lembur ELSE 0 END) as juli,
            SUM(CASE WHEN EXTRACT(MONTH FROM gaji_departemens.periode) = 8 THEN gaji_departemens.nominal_batas_lembur ELSE 0 END) as agustus,
            SUM(CASE WHEN EXTRACT(MONTH FROM gaji_departemens.periode) = 9 THEN gaji_departemens.nominal_batas_lembur ELSE 0 END) as september,
            SUM(CASE WHEN EXTRACT(MONTH FROM gaji_departemens.periode) = 10 THEN gaji_departemens.nominal_batas_lembur ELSE 0 END) as oktober,
            SUM(CASE WHEN EXTRACT(MONTH FROM gaji_departemens.periode) = 11 THEN gaji_departemens.nominal_batas_lembur ELSE 0 END) as november,
            SUM(CASE WHEN EXTRACT(MONTH FROM gaji_departemens.periode) = 12 THEN gaji_departemens.nominal_batas_lembur ELSE 0 END) as desember'
        )
        ->leftJoin('departemens', 'gaji_departemens.departemen_id', 'departemens.id_departemen');

        if(auth()->user()->hasRole('personalia') || (auth()->user()->karyawan && (auth()->user()->karyawan->posisi[0]->jabatan_id == 2 && auth()->user()->karyawan->posisi[0]->organisasi_id) || auth()->user()->karyawan->posisi[0]->jabatan_id == 5)){
            $data->where('gaji_departemens.organisasi_id', $organisasi_id);
        }

        if(auth()->user()->hasRole('atasan') && (auth()->user()->karyawan && (auth()->user()->karyawan->posisi[0]->jabatan_id == 3 || auth()->user()->karyawan->posisi[0]->jabatan_id == 4))) {
            $posisi = auth()->user()->karyawan->posisi;
            if($posisi[0]->jabatan_id == 3){
                foreach($posisi as $p){
                    if ($p->departemen_id !== null) {
                        $departemen_id[] = $p->departemen_id;
                    }
                }
                $data->whereIn('gaji_departemens.departemen_id', $departemen_id);
            } else {
                $data->where('gaji_departemens.departemen_id', auth()->user()->karyawan->posisi[0]->departemen_id);
            }
        }
        
        $data->whereNotNull('departemens.nama')
        ->whereYear('gaji_departemens.periode', $year)
        ->whereMonth('gaji_departemens.periode', $month);

        return $data->first();
    }

    public static function getCurrentMonthNominalBatasPerDepartemen()
    {
        $organisasi_id = auth()->user()->organisasi_id;
        $year = date('Y');
        $month = date('m');
        $data = self::selectRaw(
            'departemens.nama as departemen,
            departemens.id_departemen,
            SUM(gaji_departemens.nominal_batas_lembur) as nominal_batas_lembur'
        )
        ->leftJoin('departemens', 'gaji_departemens.departemen_id', 'departemens.id_departemen');

        if(auth()->user()->hasRole('personalia') || (auth()->user()->karyawan && (auth()->user()->karyawan->posisi[0]->jabatan_id == 2 && auth()->user()->karyawan->posisi[0]->organisasi_id) || auth()->user()->karyawan->posisi[0]->jabatan_id == 5)){
            $data->where('gaji_departemens.organisasi_id', $organisasi_id);
        }

        if(auth()->user()->hasRole('atasan') && (auth()->user()->karyawan && (auth()->user()->karyawan->posisi[0]->jabatan_id == 3 || auth()->user()->karyawan->posisi[0]->jabatan_id == 4))) {
            $posisi = auth()->user()->karyawan->posisi;
            if($posisi[0]->jabatan_id == 3){
                foreach($posisi as $p){
                    if ($p->departemen_id !== null) {
                        $departemen_id[] = $p->departemen_id;
                    }
                }
                $data->whereIn('gaji_departemens.departemen_id', $departemen_id);
            } else {
                $data->where('gaji_departemens.departemen_id', auth()->user()->karyawan->posisi[0]->departemen_id);
            }
        }
        
        $data->whereNotNull('departemens.nama')
        ->whereMonth('gaji_departemens.periode', $month)
        ->whereYear('gaji_departemens.periode', $year)
        ->groupBy('departemens.nama','departemens.id_departemen');

        return $data->get();
    }
}
