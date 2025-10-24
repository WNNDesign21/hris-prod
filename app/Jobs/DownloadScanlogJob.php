<?php

namespace App\Jobs;

use Throwable;
use App\Models\User;
use App\Models\Attendance\Device;
use App\Models\Attendance\Scanlog;
use Illuminate\Support\Facades\DB;
use App\Jobs\SummarizeAttendanceJob;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DownloadScanlogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $organisasi_id, $cloud_id, $start_date, $end_date, $device_id, $user;
    public $timeout = 1800;

    /**
     * Create a new job instance.
     */
    public function __construct($organisasi_id, $cloud_id, $start_date, $end_date, $device_id, User $user)
    {
        $this->organisasi_id = $organisasi_id;
        $this->cloud_id = $cloud_id;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->device_id = $device_id;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::beginTransaction();
            activity('download_scanlog')->log('Download scanlog from device_id: '.$this->device_id.' start_date: '.$this->start_date.' end_date: '.$this->end_date);
            
            // Ambil device dari DB
            $device = Device::find($this->device_id);
            if (!$device || !$device->device_sn) {
                DB::rollBack();
                activity('error_download_scanlog')->log('Device not found or missing device_sn');
                return;
            }
            $device_sn = $device->device_sn;

            // Ambil data dari SQL Server (mesin fingerprint)
            $rawData = DB::connection('sqlsrv')
                ->table('USERINFO')
                ->join('CHECKINOUT', 'USERINFO.USERID', '=', 'CHECKINOUT.USERID')
                ->select(
                    'USERINFO.USERID',
                    'USERINFO.BADGENUMBER',
                    'CHECKINOUT.CHECKTIME',
                    'CHECKINOUT.VERIFYCODE',
                    'CHECKINOUT.sn',
                    'CHECKINOUT.CHECKTYPE'
                )
                ->where('CHECKINOUT.sn', $device_sn)
                ->whereBetween('CHECKINOUT.CHECKTIME', [$this->start_date, $this->end_date])
                ->get();

            // Transform data siap insert
            $dataToInsert = $rawData->map(function ($item) {
                return [
                    'pin' => $item->BADGENUMBER,
                    'scan_date' => $item->CHECKTIME,
                    'scan_status' => $item->CHECKTYPE === 'I' ? 0 : 1,
                    'verify' => $item->VERIFYCODE,
                    'device_id' => $this->device_id,
                    'organisasi_id' => $this->organisasi_id,
                    'start_date_scan' => $this->start_date,
                    'end_date_scan' => $this->end_date,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            // HAPUS data lama hanya setelah berhasil mengambil data baru
            Scanlog::where('device_id', $this->device_id)
                ->whereBetween('scan_date', [$this->start_date, $this->end_date])
                ->whereIn('verify', [0, 1, 2, 3, 4, 5, 6])
                ->delete();

            // Insert batch ke tabel Scanlog
            Scanlog::insert($dataToInsert);

            activity('success_download_scanlog')->log('Download scanlog from device_id: '.$this->device_id.' start_date: '.$this->start_date.' end_date: '.$this->end_date);
            DB::commit();

            // Ambil pin untuk proses summarize
            $newScanlog = Scanlog::where('device_id', $this->device_id)
                ->whereBetween('scan_date', [$this->start_date, $this->end_date])
                ->pluck('pin')
                ->toArray();

            if ($this->start_date == $this->end_date) {
                SummarizeAttendanceJob::dispatch($newScanlog, $this->organisasi_id,  $this->user, $this->start_date);
            } else {
                SummarizeAttendanceJob::dispatch($newScanlog, $this->organisasi_id,  $this->user, $this->start_date);
                SummarizeAttendanceJob::dispatch($newScanlog, $this->organisasi_id,  $this->user, $this->end_date);
            }
        } catch (Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollback();
            }
            activity('error_download_scanlog')->log($e->getMessage());
        }
    }
}