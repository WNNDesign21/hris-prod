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
use Illuminate\Support\Facades\Log;
use App\Models\Attendance\AttendanceSummary;
use App\Models\Sakite;
use App\Models\Cutie;
use App\Models\Izine;
use App\Models\DetailLembur;
use App\Models\Event;

class SummarizeAttendanceJob
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
        $this->tanggal = Carbon::parse($tanggal)->format('Y-m-d');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $row = 0;
        $currentItemPin = null;
        $this->tanggal = Carbon::parse($this->tanggal)->format('Y-m-d');
        Log::info('[SummarizeAttendanceJob] Started for date: ' . $this->tanggal . ' with ' . count($this->data) . ' items.');

        DB::beginTransaction();
        try {
            $failedDatas = [];
            $formattedDate = Carbon::createFromFormat('Y-m-d', $this->tanggal);
            $karyawans = Karyawan::whereIn('pin', $this->data)
                ->where('organisasi_id', $this->organisasi_id)
                ->get();

            // Cek jika tanggal ini adalah akhir pekan (Sabtu/Minggu)
            $isWeekend = $formattedDate->isWeekend();
            
            if ($karyawans->isNotEmpty()) {
                foreach ($karyawans as $item) {
                    $row++;
                    $currentItemPin = $item->pin;
                    Log::info('[SummarizeAttendanceJob] Processing row: ' . $row . ', PIN: ' . $currentItemPin);

                    $dataFilter = [
                        'organisasi_id' => $this->organisasi_id,
                        'karyawan_id' => $item->id_karyawan,
                        'pin' => $item->pin,
                        'tanggal' => $this->tanggal,
                    ];

                    $summaryExist = AttendanceSummary::where('karyawan_id', $item->id_karyawan)
                        ->where('organisasi_id', $this->organisasi_id)
                        ->whereMonth('periode', $formattedDate->month)
                        ->whereYear('periode', $formattedDate->year)
                        ->first();

                    $approvedSakit = Sakite::where('karyawan_id', $item->id_karyawan)
                        ->where('organisasi_id', $this->organisasi_id)
                        ->where('tanggal_mulai', '<=', $this->tanggal)
                        ->where('tanggal_selesai', '>=', $this->tanggal)
                        ->whereNotNull('legalized_by')
                        ->whereNull('rejected_by')
                        ->first();

                    if ($approvedSakit) {
                        if ($summaryExist) {
                            $summaryExist->update([
                                "tanggal".$formattedDate->day."_status" => 'S',
                                "tanggal".$formattedDate->day."_selisih" => 0,
                                "tanggal".$formattedDate->day."_in" => null,
                                "tanggal".$formattedDate->day."_out" => null,
                            ]);
                        } else {
                            $posisi = $item->posisi->first();
                            AttendanceSummary::create([
                                'karyawan_id' => $item->id_karyawan,
                                'periode' => $formattedDate->copy()->startOfMonth()->format('Y-m-d'),
                                'pin' => $item->pin,
                                'organisasi_id' => $this->organisasi_id,
                                'divisi_id' => $posisi?->divisi_id,
                                'departemen_id' => $posisi?->departemen_id,
                                'seksi_id' => $posisi?->seksi_id,
                                'jabatan_id' => $posisi?->jabatan_id,
                                "tanggal".$formattedDate->day."_status" => 'S',
                                "tanggal".$formattedDate->day."_selisih" => 0,
                                "tanggal".$formattedDate->day."_in" => null,
                                "tanggal".$formattedDate->day."_out" => null,
                            ]);
                        }
                        continue;
                    }

                    $approvedCuti = Cutie::where('karyawan_id', $item->id_karyawan)
                        ->where('organisasi_id', $this->organisasi_id)
                        ->where('rencana_mulai_cuti', '<=', $this->tanggal)
                        ->where('rencana_selesai_cuti', '>=', $this->tanggal)
                        ->whereNotNull('legalized_by')
                        ->whereNull('rejected_by')
                        ->first();

                    if ($approvedCuti) {
                        if ($summaryExist) {
                            $summaryExist->update([
                                "tanggal".$formattedDate->day."_status" => 'C',
                                "tanggal".$formattedDate->day."_selisih" => 0,
                                "tanggal".$formattedDate->day."_in" => null,
                                "tanggal".$formattedDate->day."_out" => null,
                            ]);
                        } else {
                            $posisi = $item->posisi->first();
                            AttendanceSummary::create([
                                'karyawan_id' => $item->id_karyawan,
                                'periode' => $formattedDate->copy()->startOfMonth()->format('Y-m-d'),
                                'pin' => $item->pin,
                                'organisasi_id' => $this->organisasi_id,
                                'divisi_id' => $posisi?->divisi_id,
                                'departemen_id' => $posisi?->departemen_id,
                                'seksi_id' => $posisi?->seksi_id,
                                'jabatan_id' => $posisi?->jabatan_id,
                                "tanggal".$formattedDate->day."_status" => 'C',
                                "tanggal".$formattedDate->day."_selisih" => 0,
                                "tanggal".$formattedDate->day."_in" => null,
                                "tanggal".$formattedDate->day."_out" => null,
                            ]);
                        }
                        continue;
                    }
                    
                    $approvedIzin = Izine::where('karyawan_id', $item->id_karyawan)
                        ->where('organisasi_id', $this->organisasi_id)
                        ->whereDate('rencana_mulai_or_masuk', $this->tanggal)
                        ->whereNotNull('legalized_by')
                        ->whereNull('rejected_by')
                        ->where('jenis_izin', 'TM')
                        ->first();

                    if ($approvedIzin) {
                        if ($summaryExist) {
                            $summaryExist->update([
                                "tanggal".$formattedDate->day."_status" => 'I',
                                "tanggal".$formattedDate->day."_selisih" => 0,
                                "tanggal".$formattedDate->day."_in" => null,
                                "tanggal".$formattedDate->day."_out" => null,
                            ]);
                        } else {
                            $posisi = $item->posisi->first();
                            AttendanceSummary::create([
                                'karyawan_id' => $item->id_karyawan,
                                'periode' => $formattedDate->copy()->startOfMonth()->format('Y-m-d'),
                                'pin' => $item->pin,
                                'organisasi_id' => $this->organisasi_id,
                                'divisi_id' => $posisi?->divisi_id,
                                'departemen_id' => $posisi?->departemen_id,
                                'seksi_id' => $posisi?->seksi_id,
                                'jabatan_id' => $posisi?->jabatan_id,
                                "tanggal".$formattedDate->day."_status" => 'I',
                                "tanggal".$formattedDate->day."_selisih" => 0,
                                "tanggal".$formattedDate->day."_in" => null,
                                "tanggal".$formattedDate->day."_out" => null,
                            ]);
                        }
                        continue;
                    }

                    $finalSummary = ScanlogDetail::summarizePresensi($dataFilter);

                    if ($finalSummary) {
                        $approvedLembur = DetailLembur::where('karyawan_id', $item->id_karyawan)
                            ->where('organisasi_id', $this->organisasi_id)
                            ->whereDate('rencana_mulai_lembur', $this->tanggal)
                            ->whereHas('lembur', function ($query) {
                                $query->whereNotNull('actual_legalized_by')->whereNull('rejected_by');
                            })
                            ->first();

                        $isHoliday = Event::where('organisasi_id', $this->organisasi_id)
                            ->where('tanggal_mulai', '<=', $this->tanggal)
                            ->where('tanggal_selesai', '>=', $this->tanggal)
                            ->whereIn('jenis_event', ['CB', 'LN'])
                            ->exists();

                        $isLembur = $isWeekend || $approvedLembur || $isHoliday;
                        $keterlambatan = ($isLembur || !$finalSummary->in_selisih) ? 0 : intval(Carbon::createFromFormat('H:i:s', $finalSummary->in_selisih)->minute);
                        
                        if ($summaryExist) {
                            $status = $isLembur ? 'L' : 'H';
                            $updateData = [
                                "tanggal".$formattedDate->day."_status" => $status,
                                "tanggal".$formattedDate->day."_selisih" => $keterlambatan,
                                "tanggal".$formattedDate->day."_in" => $finalSummary->in_time,
                                "tanggal".$formattedDate->day."_out" => $finalSummary->out_time,
                            ];
                            Log::info('[SummarizeAttendanceJob] Updating existing summary for PIN: ' . $currentItemPin, $updateData);
                            $summaryExist->update($updateData);
                            Log::info('[SummarizeAttendanceJob] Update successful for PIN: ' . $currentItemPin);
                        } else {
                            $status = $isLembur ? 'L' : 'H';
                            $createData = [
                                'karyawan_id' => $finalSummary->id_karyawan,
                                'periode' => Carbon::createFromFormat('Y-m-d', $this->tanggal)->startOfMonth()->format('Y-m-d'),
                                'pin' => $finalSummary->pin,
                                'organisasi_id' => $finalSummary->organisasi_id,
                                'divisi_id' => $finalSummary->divisi_id,
                                'departemen_id' => $finalSummary->departemen_id,
                                'seksi_id' => $finalSummary->seksi_id,
                                'jabatan_id' => $finalSummary->jabatan_id,
                                "tanggal".$formattedDate->day."_status" => $status,
                                "tanggal".$formattedDate->day."_selisih" => $keterlambatan,
                                "tanggal".$formattedDate->day."_in" => $finalSummary->in_time,
                                "tanggal".$formattedDate->day."_out" => $finalSummary->out_time,
                            ];
                            Log::info('[SummarizeAttendanceJob] Creating new summary for PIN: ' . $currentItemPin, $createData);
                            AttendanceSummary::create($createData);
                            Log::info('[SummarizeAttendanceJob] Create successful for PIN: ' . $currentItemPin);
                        }
                    } else {
                        $posisi = $item->posisi->first();
                        // Jika akhir pekan, anggap sebagai hari libur, bukan alpa
                        $status = $isWeekend ? 'L' : 'A';
                        if ($summaryExist) {
                            $summaryExist->update([
                                "tanggal".$formattedDate->day."_status" => $status,
                                "tanggal".$formattedDate->day."_selisih" => 0,
                                "tanggal".$formattedDate->day."_in" => null,
                                "tanggal".$formattedDate->day."_out" => null,
                            ]);
                        } else {
                            AttendanceSummary::create([
                                'karyawan_id' => $item->id_karyawan,
                                'periode' => $formattedDate->copy()->startOfMonth()->format('Y-m-d'),
                                'pin' => $item->pin,
                                'organisasi_id' => $this->organisasi_id,
                                'divisi_id' => $posisi?->divisi_id,
                                'departemen_id' => $posisi?->departemen_id,
                                'seksi_id' => $posisi?->seksi_id,
                                'jabatan_id' => $posisi?->jabatan_id,
                                "tanggal".$formattedDate->day."_status" => $status,
                                "tanggal".$formattedDate->day."_selisih" => 0,
                                "tanggal".$formattedDate->day."_in" => null,
                                "tanggal".$formattedDate->day."_out" => null,
                            ]);
                        }
                        Log::warning('[SummarizeAttendanceJob] Failed to get final summary for PIN: ' . $currentItemPin . '. Marked as ' . ($isWeekend ? 'Libur' : 'Absent') . '.');
                        $failedDatas[] = [
                            'row' => $row,
                            'error' => 'Gagal merekap data presensi - ' . $item->pin . ' - ' . $item->nama. ' - ' . $this->tanggal .' - Silahkan cek kembali settingan shift & grup karyawan ini.',
                        ];
                        continue;
                    }
                }
            } else {
                Log::warning('[SummarizeAttendanceJob] No employees found for the given PINs.');
                $failedDatas[] = [
                    'row' => $row,
                    'error' => 'Gagal merekap data presensi - Karyawan tidak ditemukan.',
                ];
            }

            if (!empty($failedDatas)) {
                foreach ($failedDatas as $failedData) {
                    activity('error_job_summarize_attendance')
                        ->causedBy($this->user)
                        ->log('Summarize attendance - ' . $failedData['error']);
                }
            }

            Log::info('[SummarizeAttendanceJob] Committing transaction.');
            DB::commit();
            Log::info('[SummarizeAttendanceJob] Finished successfully.');
            activity('job_summarize_attendance')
                ->causedBy($this->user)
                ->log('Summarize attendance - ' . count($this->data) . ' datas processed.');

        } catch (Throwable $e) {
            Log::error('[SummarizeAttendanceJob] Exception caught, rolling back transaction.', [
                'error_message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'processing_pin' => $currentItemPin,
                'processing_row' => $row
            ]);
            DB::rollBack();
            activity('error_job_summarize_attendance')
                ->causedBy($this->user)
                ->log('Summarize attendance ERROR - PIN: ' . $currentItemPin . ' - Message: ' . $e->getMessage() . ' - Line: ' . $row);
        }
    }
}