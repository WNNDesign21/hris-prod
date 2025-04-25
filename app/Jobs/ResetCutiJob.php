<?php

namespace App\Jobs;

use App\Models\Karyawan;
use App\Models\ResetCuti;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ResetCutiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $today, $month, $day;

    public $timeout = 1800;
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct($today, $month, $day)
    {
        $this->today = $today;
        $this->month = $month;
        $this->day = $day;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();
        try {
            $karyawan = Karyawan::whereMonth('tanggal_mulai', $this->month)
                    ->whereDay('tanggal_mulai', $this->day)
                    ->get();
            $reset_cuti = ResetCuti::whereDate('reset_at', $this->today)->exists();
            $reset_count = 0;

            if(!$reset_cuti) {
                if ($karyawan->isNotEmpty()) {
                    foreach ($karyawan as $kry){
                        $jatah_cuti_pribadi = 6;
                        $jatah_cuti_bersama = 6;
                        //JIKA KARYAWAN PUNYA HUTANG CUTI ATAU HUTANG CUTI > 0
                        if($kry->hutang_cuti > 0){
                            $jatah_cuti_bersama -= $kry->hutang_cuti;

                            //ATUR SISA CUTI BERSAMA
                            if($jatah_cuti_bersama < 0){
                                $jatah_cuti_bersama = 0;
                                $kry->sisa_cuti_bersama = $jatah_cuti_bersama;
                                $kry->hutang_cuti = abs($jatah_cuti_bersama);
                            } else {
                                $kry->sisa_cuti_bersama = $jatah_cuti_bersama;
                                $kry->hutang_cuti = 0;
                            }

                            //ATUR SISA CUTI PRIBADI
                            $jatah_cuti_pribadi_after = $kry->sisa_cuti_pribadi + $jatah_cuti_pribadi;
                            $jatah_cuti_pribadi_after > 6 ? ($kry->sisa_cuti_pribadi = 6) : ($kry->sisa_cuti_pribadi = $jatah_cuti_pribadi_after);

                        //JIKA KARYAWAN TIDAK PUNYA HUTANG CUTI
                        } else {

                            //ATUR SISA CUTI BERSAMA
                            $jatah_cuti_bersama_after = $kry->sisa_cuti_bersama + $jatah_cuti_bersama;
                            $jatah_cuti_bersama_after > 6 ? ($kry->sisa_cuti_bersama = 6) : ($kry->sisa_cuti_bersama = $jatah_cuti_bersama_after);

                            //ATUR SISA CUTI PRIBADI
                            $jatah_cuti_pribadi_after = $kry->sisa_cuti_pribadi + $jatah_cuti_pribadi;
                            $jatah_cuti_pribadi_after > 6 ? ($kry->sisa_cuti_pribadi = 6) : ($kry->sisa_cuti_pribadi = $jatah_cuti_pribadi_after);

                            //ATUR SISA CUTI TAHUN LALU
                            if($kry->sisa_cuti_pribadi > 0){
                                $kry->sisa_cuti_tahun_lalu = $kry->sisa_cuti_pribadi;
                                $kry->expired_date_cuti_tahun_lalu = now()->addMonths(3);
                            }
                        }
                        $reset_count++;
                        activity('automatic_reset_cuti')->log('Reset Cuti Karyawan '.$kry->id_karyawan.' - '.$kry->nama.' berhasil dilakukan');
                        $kry->save();
                    }
                } else {
                    activity('automatic_reset_cuti')->log('Tidak ada karyawan yang memiliki tanggal_mulai '. $this->today);
                }

                ResetCuti::create([
                    'reset_at' => now(),
                    'reset_count' => $reset_count
                ]);
                activity('automatic_reset_cuti')->log('Pembuatan Dokumen Reset Cuti tanggal '. $this->today);
            } else {
                activity('automatic_reset_cuti')->log('Reset cuti untuk tanggal '. $this->today.' sudah dilakukan');
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            activity('error_automatic_reset_cuti')->log('Error Reset Cuti Karyawan per tanggal -'. $this->today.'- '.$e->getMessage());
        }
    }
}
