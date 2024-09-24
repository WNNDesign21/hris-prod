<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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

        Cutie::where('status_cuti', 'SCHEDULED')
            ->whereDate('rencana_mulai_cuti', $today)
            ->update([
            'status_cuti' => 'ON LEAVE',
            'aktual_mulai_cuti' => $today
            ]);
        $this->info('Status cuti karyawan berhasil diperbarui');
    }
}
