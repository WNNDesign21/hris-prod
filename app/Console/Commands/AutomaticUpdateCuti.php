<?php

namespace App\Console\Commands;

use App\Jobs\UpdateCutiJob;
use Illuminate\Console\Command;

class AutomaticUpdateCuti extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cutie:automatic-update-cuti';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah otomatis untuk mengupdate status cuti karyawan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = date('Y-m-d');
        try {
            activity('automatic_update_cuti')->log('Start Update Cuti Karyawan per tanggal -'. $today);
            UpdateCutiJob::dispatch($today);
        } catch (Exception $e) {
            activity('automatic_update_cuti')->log('Error Update Cuti Karyawan per tanggal -'. $today.'- '.$e->getMessage());
        }
    }
}
