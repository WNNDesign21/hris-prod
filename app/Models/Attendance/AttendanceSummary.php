<?php

namespace App\Models\Attendance;

use App\Models\Seksi;
use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Organisasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceSummary extends Model
{
    use SoftDeletes;
    protected $table = 'attendance_summaries';
    protected $primaryKey = 'id_att_summary';

    protected $fillable = [
        'karyawan_id',
        'pin',
        'periode',
        'organisasi_id',
        'divisi_id',
        'departemen_id',
        'seksi_id',
        'jabatan_id',
        'total_absen',
        'total_sakit',
        'total_izin',
        'total_hadir',
        'keterlambatan',
        'is_cutoff',
        'tanggal1_status',
        'tanggal1_in',
        'tanggal1_out',
        'tanggal1_selisih',
        'tanggal2_status',
        'tanggal2_in',
        'tanggal2_out',
        'tanggal2_selisih',
        'tanggal3_status',
        'tanggal3_in',
        'tanggal3_out',
        'tanggal3_selisih',
        'tanggal4_status',
        'tanggal4_in',
        'tanggal4_out',
        'tanggal4_selisih',
        'tanggal5_status',
        'tanggal5_in',
        'tanggal5_out',
        'tanggal5_selisih',
        'tanggal6_status',
        'tanggal6_in',
        'tanggal6_out',
        'tanggal6_selisih',
        'tanggal7_status',
        'tanggal7_in',
        'tanggal7_out',
        'tanggal7_selisih',
        'tanggal8_status',
        'tanggal8_in',
        'tanggal8_out',
        'tanggal8_selisih',
        'tanggal9_status',
        'tanggal9_in',
        'tanggal9_out',
        'tanggal9_selisih',
        'tanggal10_status',
        'tanggal10_in',
        'tanggal10_out',
        'tanggal10_selisih',
        'tanggal11_status',
        'tanggal11_in',
        'tanggal11_out',
        'tanggal11_selisih',
        'tanggal12_status',
        'tanggal12_in',
        'tanggal12_out',
        'tanggal12_selisih',
        'tanggal13_status',
        'tanggal13_in',
        'tanggal13_out',
        'tanggal13_selisih',
        'tanggal14_status',
        'tanggal14_in',
        'tanggal14_out',
        'tanggal14_selisih',
        'tanggal15_status',
        'tanggal15_in',
        'tanggal15_out',
        'tanggal15_selisih',
        'tanggal16_status',
        'tanggal16_in',
        'tanggal16_out',
        'tanggal16_selisih',
        'tanggal17_status',
        'tanggal17_in',
        'tanggal17_out',
        'tanggal17_selisih',
        'tanggal18_status',
        'tanggal18_in',
        'tanggal18_out',
        'tanggal18_selisih',
        'tanggal19_status',
        'tanggal19_in',
        'tanggal19_out',
        'tanggal19_selisih',
        'tanggal20_status',
        'tanggal20_in',
        'tanggal20_out',
        'tanggal20_selisih',
        'tanggal21_status',
        'tanggal21_in',
        'tanggal21_out',
        'tanggal21_selisih',
        'tanggal22_status',
        'tanggal22_in',
        'tanggal22_out',
        'tanggal22_selisih',
        'tanggal23_status',
        'tanggal23_in',
        'tanggal23_out',
        'tanggal23_selisih',
        'tanggal24_status',
        'tanggal24_in',
        'tanggal24_out',
        'tanggal24_selisih',
        'tanggal25_status',
        'tanggal25_in',
        'tanggal25_out',
        'tanggal25_selisih',
        'tanggal26_status',
        'tanggal26_in',
        'tanggal26_out',
        'tanggal26_selisih',
        'tanggal27_status',
        'tanggal27_in',
        'tanggal27_out',
        'tanggal27_selisih',
        'tanggal28_status',
        'tanggal28_in',
        'tanggal28_out',
        'tanggal28_selisih',
        'tanggal29_status',
        'tanggal29_in',
        'tanggal29_out',
        'tanggal29_selisih',
        'tanggal30_status',
        'tanggal30_in',
        'tanggal30_out',
        'tanggal30_selisih',
        'tanggal31_status',
        'tanggal31_in',
        'tanggal31_out',
        'tanggal31_selisih',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
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

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id', 'id_jabatan');
    }

    public function seksi()
    {
        return $this->belongsTo(Seksi::class, 'seksi_id', 'id_seksi');
    }
}
