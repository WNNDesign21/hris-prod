<?php

namespace App\Jobs;

use Throwable;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Attendance\AttendanceSummary;

class GenerateSummarizeAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $dates, $organisasi_id, $user, $karyawan_id, $type;

    public $timeout = 1800;
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(array $dates, int $organisasi_id, User $user = null, string $karyawan_id, string $type)
    {
        $this->dates = $dates;
        $this->organisasi_id = $organisasi_id;
        $this->user = $user;
        $this->karyawan_id = $karyawan_id;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();
        try {
            $row = 0;
            $failedDatas = [];
            $karyawan = Karyawan::find($this->karyawan_id);
            if ($karyawan) {
                $posisi = $karyawan->posisi[0];
                if ($posisi) {
                    foreach ($this->dates as $date) {
                        $row++;
                        $formattedDate = Carbon::createFromFormat('Y-m-d', $date);
                        $summaryExist = AttendanceSummary::where('karyawan_id', $this->karyawan_id)
                            ->where('organisasi_id', $this->organisasi_id)
                            ->whereMonth('periode', $formattedDate->month)
                            ->whereYear('periode', $formattedDate->year)
                            ->first();

                        if ($summaryExist) {
                            $summaryExist->update([
                                "tanggal".$formattedDate->day."_status" => $this->type,
                                "tanggal".$formattedDate->day."_selisih" => 0,
                                "tanggal".$formattedDate->day."_in" => '00:00',
                                "tanggal".$formattedDate->day."_out" => '00:00',
                            ]);
                        } else {
                            $periode = Carbon::createFromFormat('Y-m-d', $date)->startOfMonth()->format('Y-m-d');
                            $newSummary = AttendanceSummary::create([
                                'karyawan_id' => $this->karyawan_id,
                                'periode' => $periode,
                                'pin' => $karyawan->pin,
                                'organisasi_id' => $this->organisasi_id,
                                'divisi_id' => $posisi?->divisi_id,
                                'departemen_id' => $posisi?->departemen_id,
                                'seksi_id' => $posisi?->seksi_id,
                                'jabatan_id' => $posisi?->jabatan_id,
                                "tanggal".$formattedDate->day."_status" => $this->type,
                                "tanggal".$formattedDate->day."_selisih" => 0,
                                "tanggal".$formattedDate->day."_in" => '00:00',
                                "tanggal".$formattedDate->day."_out" => '00:00',
                            ]);
                        }
                    }
                } else {
                    $failedDatas[] = [
                        'row' => $row,
                        'error' => 'Gagal merekap data presensi - Karyawan tidak memiliki posisi.',
                    ];
                }
            } else {
                $failedDatas[] = [
                    'row' => $row,
                    'error' => 'Gagal merekap data presensi - Karyawan tidak ditemukan.',
                ];
            }

            if (!empty($failedDatas)) {
                foreach ($failedDatas as $failedData) {
                    activity('error_job_generate_summarize_attendance')
                        ->causedBy($this->user)
                        ->log('Generate Summarize attendance - ' . $failedData['error']);
                }
            }

            activity('job_generate_summarize_attendance')
                ->causedBy($this->user)
                ->log('Generate Summarize attendance - ' . count($this->dates) . ' tanggal');
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            activity('error_generate_job_summarize_attendance')
                ->causedBy($this->user)
                ->log('Generate Summarize attendance -' . $e->getMessage() . ' - Baris' . $row);
        }
    }
}
