<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutomaticRejectCuti extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cutie:automatic-reject-cuti';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah otomatis untuk menolak cuti karyawan yang tidak ada persetujuan dari atasan sampai hari H renacana cuti';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            $today = date('Y-m-d');
            $cuti = Cutie::where('status_dokumen', 'WAITING')->whereDate('rencana_mulai_cuti', $today);
            $data_cuti = $cuti->get();

            $cuti->update([
                'status_dokumen' => 'REJECTED',
                'rejected_at' => now(),
                'rejected_by' => 'SYSTEM',
                'rejected_note' => 'Cuti otomatis dibatalkan system karena tidak ada persetujuan dari atasan sampai hari H rencana cuti'
            ]);

            foreach ($data_cuti as $ct) {
                $karyawan = Karyawan::find($ct->karyawan_id);
                $total_sisa_cuti_pribadi = $karyawan->sisa_cuti_pribadi + $ct->durasi_cuti;
                if ($total_sisa_cuti_pribadi > 6) {
                    $karyawan->sisa_cuti_pribadi = 6;
                } else {
                    $karyawan->sisa_cuti_pribadi = $total_sisa_cuti_pribadi;
                }
                $karyawan->save();
            }
            DB::commit();
            $this->info('Sisa cuti karyawan berhasil dikembalikan dan pengajuan cuti ditolak otomatis');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
