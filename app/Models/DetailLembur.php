<?php

namespace App\Models;

use App\Models\Lembure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailLembur extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'detail_lemburs';
    protected $primaryKey = 'id_detail_lembur';

    protected $fillable = [
        'lembur_id', 
        'karyawan_id', 
        'organisasi_id', 
        'departemen_id', 
        'divisi_id', 
        'rencana_mulai_lembur', 
        'rencana_selesai_lembur', 
        'is_rencana_approved', 
        'aktual_mulai_lembur', 
        'aktual_selesai_lembur', 
        'is_aktual_approved', 
        'durasi', 
        'durasi_istirahat',
        'durasi_konversi_lembur',
        'gaji_lembur',
        'uang_makan',
        'deskripsi_pekerjaan', 
        'keterangan', 
        'nominal'
    ];

    public function lembur()
    {
        return $this->belongsTo(Lembure::class, 'lembur_id', 'id_lembur');
    }

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

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }

    public static function getSlipLemburPerDepartemen($karyawan_id, $date)
    {
        $data = self::select('detail_lemburs.*')
        ->leftJoin('lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')
        ->leftJoin('karyawans', 'karyawans.id_karyawan', 'detail_lemburs.karyawan_id')
        ->leftJoin('departemens', 'departemens.id_departemen', 'detail_lemburs.departemen_id')
        ->leftJoin('divisis', 'divisis.id_divisi', 'detail_lemburs.divisi_id')
        ->leftJoin('karyawan_posisi', 'karyawan_posisi.karyawan_id', 'karyawans.id_karyawan')
        ->leftJoin('posisis', 'posisis.id_posisi', 'karyawan_posisi.posisi_id')
        ->leftJoin('setting_lembur_karyawans', 'setting_lembur_karyawans.karyawan_id', 'detail_lemburs.karyawan_id');

        $data->where('detail_lemburs.organisasi_id', auth()->user()->organisasi_id)
        ->whereNotNull('lemburs.actual_legalized_by')
        ->where('lemburs.status', 'COMPLETED')
        ->where('detail_lemburs.karyawan_id', $karyawan_id)
        ->whereDate('detail_lemburs.aktual_mulai_lembur', $date)
        ->orderBy('detail_lemburs.aktual_mulai_lembur');

        return $data->first();
    }

    public static function getReportMonthlyPerDepartemen($month, $year)
    {
        $data = self::selectRaw('
            posisis.jabatan_id,
            karyawans.nama,
            departemens.nama as departemen,
            posisis.nama as posisi,
            setting_lembur_karyawans.gaji,
            TRUNC(setting_lembur_karyawans.gaji) / 173 as upah_lembur_per_jam,
            TRUNC(SUM(TRUNC(detail_lemburs.durasi, 2) / 60),2) as total_jam_lembur,
            TRUNC(
                SUM(
                    CASE WHEN lemburs.jenis_hari = \'WE\' THEN 
                        CASE 
                            WHEN detail_lemburs.durasi < 540 THEN TRUNC(detail_lemburs.durasi, 2) * 2 / 60
                            WHEN detail_lemburs.durasi < 600 THEN TRUNC((TRUNC(detail_lemburs.durasi, 2) - 480) * 3 + 960) / 60
                            ELSE TRUNC((TRUNC(detail_lemburs.durasi, 2) - 540) * 4 + 1140) / 60
                        END
                    ELSE
                        CASE 
                            WHEN detail_lemburs.durasi > 0 THEN TRUNC(1 * 1.5 + ((TRUNC(detail_lemburs.durasi, 2) - 1) * 2)) / 60
                            ELSE 0
                        END
                    END
                ), 2
            ) as konversi_jam_lembur,
            CASE WHEN posisis.jabatan_id >= 5 THEN
                SUM(detail_lemburs.nominal) - SUM(CASE 
                    WHEN posisis.jabatan_id >= 5 AND detail_lemburs.durasi >= 4 THEN 15000 
                    ELSE 0 
                END)
            ELSE
                SUM(detail_lemburs.nominal)
            END as gaji_lembur,
            SUM(CASE 
                    WHEN posisis.jabatan_id >= 5 AND detail_lemburs.durasi >= 4 THEN 15000 
                    ELSE 0 
                END) as uang_makan,
            SUM(detail_lemburs.nominal) as total_gaji_lembur
        ')
        ->leftJoin('lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')
        ->leftJoin('karyawans', 'karyawans.id_karyawan', 'detail_lemburs.karyawan_id')
        ->leftJoin('departemens', 'departemens.id_departemen', 'detail_lemburs.departemen_id')
        ->leftJoin('divisis', 'divisis.id_divisi', 'detail_lemburs.divisi_id')
        ->leftJoin('karyawan_posisi', 'karyawan_posisi.karyawan_id', 'karyawans.id_karyawan')
        ->leftJoin('posisis', 'posisis.id_posisi', 'karyawan_posisi.posisi_id')
        ->leftJoin('setting_lembur_karyawans', 'setting_lembur_karyawans.karyawan_id', 'detail_lemburs.karyawan_id')

        ->where('detail_lemburs.organisasi_id', auth()->user()->organisasi_id)
        ->whereNotNull('lemburs.actual_legalized_by')
        ->where('lemburs.status', 'COMPLETED')
        ->whereMonth('detail_lemburs.aktual_mulai_lembur', $month)
        ->whereYear('detail_lemburs.aktual_mulai_lembur', $year)
        ->groupBy('posisis.jabatan_id','karyawans.nama', 'departemens.nama','detail_lemburs.departemen_id', 'posisis.nama', 'setting_lembur_karyawans.gaji')
        ->orderBy('detail_lemburs.departemen_id');
        
        return $data->get();
    }
}
