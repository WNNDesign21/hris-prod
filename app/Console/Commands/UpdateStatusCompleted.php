<?php

namespace App\Console\Commands;

use App\Models\Cutie;
use Illuminate\Console\Command;

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
        
        Cutie::where('status_cuti', 'ON LEAVE')
            ->whereDate('rencana_selesai_cuti', $today)
            ->update([
            'status_cuti' => 'COMPLETED',
            'aktual_selesai_cuti' => $today
            ]);

        $this->info('Status cuti karyawan berhasil diperbarui');
    }
}
