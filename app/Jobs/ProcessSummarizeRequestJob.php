<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use App\Models\Karyawan;
use App\Models\User;
use Throwable;

class ProcessSummarizeRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800;

    protected $periode;
    protected $departemen;
    protected $organisasi_id;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($periode, $departemen, $organisasi_id, User $user)
    {
        $this->periode = $periode;
        $this->departemen = $departemen;
        $this->organisasi_id = $organisasi_id;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $carbon_periode = Carbon::createFromFormat('Y-m', $this->periode);
            $month = $carbon_periode->format('m');
            $year = $carbon_periode->format('Y');
            $daysInMonth = $carbon_periode->daysInMonth;

            $karyawanQuery = Karyawan::where('organisasi_id', $this->organisasi_id);

            if (!empty($this->departemen)) {
                $karyawanQuery->whereHas('posisi', function ($query) {
                    $query->whereIn('departemen_id', $this->departemen);
                });
            }

            $karyawans = $karyawanQuery->get();

            if ($karyawans->isEmpty()) {
                // Optional: Log that no employees were found
                return;
            }

            $pins = $karyawans->pluck('pin')->toArray();

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $tanggal = Carbon::create($year, $month, $day)->format('Y-m-d');
                SummarizeAttendanceJob::dispatch($pins, $this->organisasi_id, $this->user, $tanggal);
            }
        } catch (Throwable $e) {
            // Log the exception
            activity('error_job_process_summarize_request')
                ->causedBy($this->user)
                ->log('ProcessSummarizeRequestJob failed: ' . $e->getMessage());
        }
    }
}