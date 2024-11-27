<?php

namespace App\Console\Commands;

use Exception;
use App\Models\Cutie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;


class UpdateStatusCompleted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cutie:update-status-completed';

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
            $cuti = Cutie::where('status_cuti', 'ON LEAVE')
                ->whereDate('rencana_selesai_cuti', $today)
                ->update([
                'status_cuti' => 'COMPLETED',
                'aktual_selesai_cuti' => $today
            ]);
            activity('update_status_completed')->log('Update Status Completed Cuti Otomatis per tanggal -'. $today);
            DB::commit();
            $this->info('Status cuti karyawan berhasil diperbarui');
        } catch (Exception $e) {
            DB::rollBack();
            $this->error('Gagal memperbarui status cuti karyawan');
        }
    }
}
