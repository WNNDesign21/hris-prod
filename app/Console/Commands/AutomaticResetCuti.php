<?php

namespace App\Console\Commands;

use Exception;
use App\Models\Karyawan;
use App\Models\ResetCuti;
use App\Jobs\ResetCutiJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;


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
        $month = now()->month;
        $day = now()->day;
        try {
            activity('automatic_reset_cuti')->log('Start Reset Cuti Karyawan per tanggal -'. $today);
            ResetCutiJob::dispatch($today, $month, $day);
        } catch (Exception $e) {
            activity('automatic_reset_cuti')->log('Error Reset Cuti Karyawan per tanggal -'. $today.'- '.$e->getMessage());
        }
    }
}
