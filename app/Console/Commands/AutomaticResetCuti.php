<?php

namespace App\Console\Commands;

use Exception;
use App\Models\Karyawan;
use App\Models\ResetCuti;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutomaticResetCuti extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cutie:automatic-reset-cuti';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah otomatis untuk mereset cuti karyawan setiap menyentuh satu tahun masa kerja';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day', strtotime($today)));

        DB::beginTransaction();
        try {
            $karyawan = Karyawan::aktif()->get();
            $reset_cuti_today = ResetCuti::whereDate('reset_at', $today)->exists();
            $reset_cuti_yesterday = ResetCuti::whereDate('reset_at', $yesterday)->exists();
            $reset_count = 0;
                foreach ($karyawan as $kry){
                    $jatah_cuti_pribadi = 6;
                    $jatah_cuti_bersama = 6;
                    if(($kry->tanggal_mulai == $today && !$reset_cuti_today) || ($kry->tanggal_mulai == $yesterday && !$reset_cuti_yesterday)){

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
                    } 
                    $kry->save();
                }

                activity('automatic_reset_cuti')->withProperties($karyawan)->performedOn($karyawan)->log('Reset Cuti Karyawan per tanggal -'. $today);
                
                if(!$reset_cuti_today){
                    ResetCuti::create([
                        'reset_at' => now(),
                        'reset_count' => $reset_count
                    ]);
                }
                
                DB::commit();
                $this->info('Sisa cuti karyawan berhasil direset');
        } catch (Exception $e) {
            DB::rollBack();
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
