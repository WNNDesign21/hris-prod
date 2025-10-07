<?php

namespace App\Jobs;

use Exception;
use Carbon\Carbon;
use App\Models\Cutie;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Jobs\GenerateSummarizeAttendanceJob;

class UpdateCutiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $today;
    public $timeout = 1800;
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct($today)
    {
        $this->today = $today;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();
        try {
            $mustRejectCuti = Cutie::with('karyawan')->where('status_dokumen', 'WAITING')->whereDate('rencana_mulai_cuti', '<=' , $this->today)->where(function($query){
                    $query->where('status_cuti', '!=', 'CANCELED')
                    ->orWhereNull('status_cuti');
                })->get();

            $mustOnleaveCuti = Cutie::where('status_cuti', 'SCHEDULED')
                    ->whereDate('rencana_mulai_cuti', $this->today)
                    ->where('status_dokumen', 'APPROVED')
                    ->get();

            $mustCompletedCuti = Cutie::where('status_cuti', 'ON LEAVE')
                ->whereDate('rencana_selesai_cuti', Carbon::parse($this->today)->subDay())
                ->get();

            // REJECT CUTI
            if ($mustRejectCuti->isNotEmpty()) {
                foreach ($mustRejectCuti as $data) {
                    $data->status_dokumen = 'REJECTED';
                    $data->rejected_at = now();
                    $data->rejected_by = 'SYSTEM';
                    $data->rejected_note = 'Cuti otomatis dibatalkan system karena tidak ada persetujuan dari atasan sampai hari H rencana cuti';
                    $data->save();
                    activity('automatic_reject_cuti')->log('Reject Cuti ID -'. $data->id_cuti .' Karyawan ID -'. $data->karyawan_id .' per tanggal -'. $this->today);

                    $karyawan = $data->karyawan;
                    if ($data->karyawan) {
                        if ($data->jenis_cuti == 'PRIBADI') {
                            if ($data->penggunaan_sisa_cuti == 'TB') {
                                $total_sisa_cuti_pribadi = $karyawan->sisa_cuti_pribadi + $data->durasi_cuti;
                                $karyawan->sisa_cuti_pribadi = min($total_sisa_cuti_pribadi, 6);
                            } else {
                                $total_sisa_cuti_tahun_lalu = $karyawan->sisa_cuti_tahun_lalu + $data->durasi_cuti;
                                $karyawan->sisa_cuti_tahun_lalu = min($total_sisa_cuti_tahun_lalu, 6);
                            }
                            $karyawan->save();
                            activity('automatic_reject_cuti')->log('Mengembalikan jatah cuti yang terpakai oleh karyawan ID -'. $data->karyawan_id .' per tanggal -'. $this->today);
                        }
                    }
                }
                activity('automatic_reject_cuti')->log('Reject Cuti Karyawan per tanggal -'. $this->today.' berhasil dilakukan.');
            } else {
                activity('automatic_reject_cuti')->log('Tidak ada cuti yang harus di reject per tanggal -'. $this->today);
            }

            // ON LEAVE CUTI
            if ($mustOnleaveCuti->isNotEmpty()) {
                foreach ($mustOnleaveCuti as $data) {
                    $data->status_cuti = 'ON LEAVE';
                    $data->aktual_mulai_cuti = $data->rencana_mulai_cuti;
                    $data->save();
                    activity('update_cuti_status_onleave')->log('Update Status Onleave Cuti Otomatis karyawan ID -'. $data->karyawan_id .' per tanggal -'. $this->today);

                    $organisasi_id = $data->organisasi_id;
                    $dateArray = [];
                    $startDate = Carbon::parse($data->rencana_mulai_cuti);
                    $endDate = Carbon::parse($data->rencana_selesai_cuti);

                    while ($startDate->lte($endDate)) {
                        $dateArray[] = $startDate->format('Y-m-d');
                        $startDate->addDay();
                    }

                    $dateArray = array_unique($dateArray);
                    if (!empty($dateArray)) {
                        GenerateSummarizeAttendanceJob::dispatch($dateArray, $organisasi_id, null, $data->karyawan_id, 'C');
                    }
                }
                activity('update_cuti_status_onleave')->log('Update Status Onleave Cuti Otomatis per tanggal -'. $this->today.' berhasil dilakukan.');
            } else {
                activity('update_cuti_status_onleave')->log('Tidak ada cuti yang harus di update status onleave per tanggal -'. $this->today);
            }

            // COMPLETED CUTI
            if ($mustCompletedCuti->isNotEmpty()) {
                foreach ($mustCompletedCuti as $data) {
                    $data->status_cuti = 'COMPLETED';
                    $data->aktual_selesai_cuti = $data->rencana_selesai_cuti;
                    $data->save();
                    activity('update_cuti_status_completed')->log('Update Status Completed Cuti Otomatis karyawan ID -'. $data->karyawan_id .' per tanggal -'. $this->today);
                }
                activity('update_cuti_status_completed')->log('Update Status Completed Cuti Otomatis per tanggal -'. $this->today.' berhasil dilakukan.');
            } else {
                activity('update_cuti_status_completed')->log('Tidak ada cuti yang harus di update status completed per tanggal -'. $this->today);
            }

            activity('automatic_update_cuti')->log('Update status cuti otomatis per tanggal -'. $this->today.' berhasil dilakukan.');
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            activity('error_automatic_update_cuti')->log('Gagal update cuti karyawan per tanggal -'. $this->today. ' Error: '. $e->getMessage());
        }
    }
}
