<?php

namespace App\Jobs;

use Throwable;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Attendance\AttendanceSummary;

class RekapAttendanceHarianJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $organisasi_id, $user, $periode;

    public $timeout = 1800;
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(int $organisasi_id, User $user, string $periode)
    {
        $this->organisasi_id = $organisasi_id;
        $this->user = $user;
        $this->periode = $periode;
    }

    // UNFINISHED
    public function handle(): void
    {
        DB::beginTransaction();
        try {
            $month = Carbon::createFromFormat('Y-m-d', $this->periode)->month;
            $year = Carbon::createFromFormat('Y-m-d', $this->periode)->year;
            $dayCount = Carbon::createFromFormat('Y-m-d', $this->periode)->daysInMonth;
            $dataSummary = AttendanceSummary::where('organisasi_id', $this->organisasi_id)
                ->whereYear('periode', $year)
                ->whereMonth('periode', $month)
                ->where('is_cutoff', 'N')
                ->get();

            if ($dataSummary->isNotEmpty()) {
                foreach ($dataSummary as $item) {
                    $total_hadir = 0;
                    $total_izin = 0;
                    $total_sakit = 0;
                    $total_absen = 0;
                    $total_keterlambatan = 0;

                    for ($i = 1; $i <= $dayCount; $i++) {

                    }
                }

            } else {
                DB::rollback();
                activity('error_job_rekap_attendance_harian')
                            ->causedBy($this->user)
                            ->log('Rekap attendance harian - ');
            }

            DB::commit();
            activity('job_rekap_attendance_harian')
                    ->causedBy($this->user)
                    ->log('Rekap attendance harian - ' . $dataSummary->count() . ' datas');
        } catch (Throwable $e) {
            DB::rollback();
            activity('error_job_rekap_attendance_harian')
                ->causedBy($this->user)
                ->log('Rekap attendance harian - ' . $e->getMessage());
        }
    }
}
