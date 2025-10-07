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
            $cuties = Cutie::where('status_cuti', 'ON LEAVE')
                ->whereDate('rencana_selesai_cuti', $today)
                ->get();

            foreach ($cuties as $cutie) {
                $cutie->update([
                    'status_cuti' => 'COMPLETED',
                    'aktual_selesai_cuti' => $today
                ]);
            }
            activity('update_status_completed')->log('Update Status Completed Cuti Otomatis per tanggal -'. $today);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            activity('error_update_status_completed')->log('Error Updating Status Completed Cuti Otomatis per tanggal -'. $today);
        }
    }
}
