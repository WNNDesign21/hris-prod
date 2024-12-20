<?php

namespace App\Console\Commands;

use Exception;
use App\Models\Cutie;
use App\Models\Karyawan;
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
            $cuti = Cutie::where('status_dokumen', 'WAITING')->whereDate('rencana_mulai_cuti', $today)->where(function($query){
                $query->where('status_cuti', '!=', 'CANCELED')
                ->orWhereNull('status_cuti');
            });
            $data_cuti = $cuti->get();

            if($data_cuti){
                foreach ($data_cuti as $ct) {

                    //KONDISI JIKA ADA SALAH SATU YANG SUDAH DISETUJUI
                    if($ct->checked1_by || $ct->checked2_by || $ct->approved_by){
                        if($ct->checked1_by == null){
                            $ct->checked1_by = 'SYSTEM';
                            $ct->checked1_at = now();
                        } 
                        if ($ct->checked2_by == null){
                            $ct->checked2_by = 'SYSTEM';
                            $ct->checked2_at = now();
                        } 
                        
                        if ($ct->approved_by == null){
                            $ct->approved_by = 'SYSTEM';
                            $ct->approved_at = now();
                        }
                        $ct->status_dokumen = 'APPROVED';
                        $ct->status_cuti = 'SCHEDULED';
                        $ct->legalized_by = 'HRD & GA (SYSTEM)';
                        $ct->legalized_at = now();
                        $ct->save();

                    //KONDISI JIKA TIDAK ADA YANG DISETUJUI
                    } elseif ($ct->checked1_by == null && $ct->checked2_by == null && $ct->approved_by == null){
                        $ct->update([
                            'status_dokumen' => 'REJECTED',
                            'rejected_at' => now(),
                            'rejected_by' => 'SYSTEM',
                            'rejected_note' => 'Cuti otomatis dibatalkan system karena tidak ada persetujuan dari atasan sampai hari H rencana cuti'
                        ]);
                        
                        $karyawan = Karyawan::find($ct->karyawan_id);
                        if($ct->jenis_cuti == 'PRIBADI'){
                            if($ct->penggunaan_sisa_cuti == 'TB'){
                                $total_sisa_cuti_pribadi = $karyawan->sisa_cuti_pribadi + $ct->durasi_cuti;
                                if ($total_sisa_cuti_pribadi > 6) {
                                    $karyawan->sisa_cuti_pribadi = 6;
                                } else {
                                    $karyawan->sisa_cuti_pribadi = $total_sisa_cuti_pribadi;
                                }
                            } else {
                                $total_sisa_cuti_tahun_lalu = $karyawan->sisa_cuti_tahun_lalu + $ct->durasi_cuti;
                                if ($total_sisa_cuti_tahun_lalu > 6) {
                                    $karyawan->sisa_cuti_tahun_lalu = 6;
                                } else {
                                    $karyawan->sisa_cuti_tahun_lalu = $total_sisa_cuti_tahun_lalu;
                                }
                            }
                            $karyawan->save();
                        }
                    }
                }
            }
            activity('automatic_reject_cuti')->log('Reject Cuti Karyawan per tanggal -'. $today);
            $this->info('Sisa cuti karyawan berhasil dikembalikan dan pengajuan cuti ditolak otomatis');
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
