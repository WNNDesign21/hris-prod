<?php

namespace App\Console\Commands;

use Exception;
use App\Models\Cutie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class UpdateStatusOnleave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cutie:update-status-onleave';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = date('Y-m-d');

        DB::beginTransaction();
        try {
            $cuties = Cutie::where('status_cuti', 'SCHEDULED')
                ->whereDate('rencana_mulai_cuti', $today)
                ->where('status_dokumen', 'APPROVED')
                ->get();

            foreach ($cuties as $cutie) {
                $cutie->status_cuti = 'ON LEAVE';
                $cutie->aktual_mulai_cuti = $today;
                $cutie->save();
            }

            activity('update_status_onleave')->log('Update Status Onleave Cuti Otomatis per tanggal -'. $today);

            DB::commit();
            $this->info('Status cuti karyawan berhasil diperbarui');
        } catch (Exception $e) {
            DB::rollBack();
            $this->error('Gagal memperbarui status cuti karyawan');
        }
    }
}
