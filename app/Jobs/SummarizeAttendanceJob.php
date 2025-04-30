<?php

namespace App\Jobs;

use Throwable;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use App\Models\Attendance\ScanlogDetail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Attendance\AttendanceSummary;

class SummarizeAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data, $organisasi_id, $user, $tanggal;

    public $timeout = 1800;
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data, int $organisasi_id, User $user, string $tanggal)
    {
        $this->data = $data;
        $this->organisasi_id = $organisasi_id;
        $this->user = $user;
        $this->tanggal = $tanggal;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();
        try {
            $row = 0;
            $summarize = [];
            $failedDatas = [];
            $formattedDate = Carbon::createFromFormat('Y-m-d', $this->tanggal);
            $karyawans = Karyawan::where('pin', '265')
                ->where('organisasi_id', $this->organisasi_id)
                ->get(['id_karyawan', 'pin', 'nama']);

            if ($karyawans) {
                foreach ($karyawans as $item) {
                    $row++;
                    $dataFilter = [];
                    $dataFilter['organisasi_id'] = $this->organisasi_id;
                    $dataFilter['karyawan_id'] = $item->id_karyawan;
                    $dataFilter['pin'] = $item->pin;
                    $dataFilter['tanggal'] = $this->tanggal;
                    $finalSummary = ScanlogDetail::summarizePresensi($dataFilter);
                    $summaryExist = AttendanceSummary::where('karyawan_id', $item->id_karyawan)
                        ->where('organisasi_id', $this->organisasi_id)
                        ->whereMonth('periode', $formattedDate->month)
                        ->whereYear('periode', $formattedDate->year)
                        ->first();

                    if ($summaryExist) {
                        if ($finalSummary) {
                            $keterlambatan = $finalSummary->in_selisih ? intval(Carbon::createFromFormat('H:i:s', $finalSummary->in_selisih)->minute) : 0;
                            $summaryExist->update([
                                "tanggal".$formattedDate->day."_status" => 'H',
                                "tanggal".$formattedDate->day."_selisih" => $keterlambatan,
                                "tanggal".$formattedDate->day."_in" => $finalSummary->in_time,
                                "tanggal".$formattedDate->day."_out" => $finalSummary->out_time,
                            ]);
                        } else {
                            $failedDatas[] = [
                                'row' => $row,
                                'error' => 'Gagal merekap data presensi - ' . $item->pin . ' - ' . $item->nama. ' - ' . $item->tanggal .' - Silahkan cek kembali settingan shift & grup karyawan ini.',
                            ];
                            continue;
                        }
                    } else {
                        if ($finalSummary) {
                            $keterlambatan = $finalSummary->in_selisih ? intval(Carbon::createFromFormat('H:i:s', $finalSummary->in_selisih)->minute) : 0;
                            $summarize[] = [
                                'karyawan_id' => $finalSummary->id_karyawan,
                                'periode' => Carbon::createFromFormat('Y-m-d', $this->tanggal)->startOfMonth()->format('Y-m-d'),
                                'pin' => $finalSummary->pin,
                                'organisasi_id' => $finalSummary->organisasi_id,
                                'divisi_id' => $finalSummary->divisi_id,
                                'departemen_id' => $finalSummary->departemen_id,
                                'seksi_id' => $finalSummary->seksi_id,
                                'jabatan_id' => $finalSummary->jabatan_id,
                                "tanggal".$formattedDate->day."_status" => "H",
                                "tanggal".$formattedDate->day."_selisih" => $keterlambatan,
                                "tanggal".$formattedDate->day."_in" => $finalSummary->in_time,
                                "tanggal".$formattedDate->day."_out" => $finalSummary->out_time,
                            ];
                        } else {
                            $failedDatas[] = [
                                'row' => $row,
                                'error' => 'Gagal merekap data presensi - ' . $item->pin . ' - ' . $item->nama. ' - ' . $item->tanggal .' - Silahkan cek kembali settingan shift & grup karyawan ini.',
                            ];
                            continue;
                        }
                    }
                }
            } else {
                $failedDatas[] = [
                    'row' => $row,
                    'error' => 'Gagal merekap data presensi - Karyawan tidak ditemukan.',
                ];
            }

            if (!empty($summarize)) {
                AttendanceSummary::insert($summarize);
            }

            if (!empty($failedDatas)) {
                foreach ($failedDatas as $failedData) {
                    activity('error_job_summarize_attendance')
                        ->causedBy($this->user)
                        ->log('Summarize attendance - ' . $failedData['error']);
                }
            }

            activity('job_summarize_attendance')
                ->causedBy($this->user)
                ->log('Summarize attendance - ' . count($this->data) . ' datas');
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            activity('error_job_summarize_attendance')
                ->causedBy($this->user)
                ->log('Summarize attendance -' . $e->getMessage() . ' - Baris' . $row);
        }
    }
}
