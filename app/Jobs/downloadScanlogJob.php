<?php

namespace App\Jobs;

use Throwable;
use GuzzleHttp\Client;
use App\Models\Attendance\Scanlog;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DownloadScanlogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $organisasi_id, $cloud_id, $start_date, $end_date, $device_id;
    public $timeout = 1800;

    /**
     * Create a new job instance.
     */
    public function __construct($organisasi_id, $cloud_id, $start_date, $end_date, $device_id)
    {
        $this->organisasi_id = $organisasi_id;
        $this->cloud_id = $cloud_id;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->device_id = $device_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();
        activity('download_scanlog')->log('Download scanlog from device_id: '.$this->device_id.' start_date: '.$this->start_date.' end_date: '.$this->end_date);
        try {
            //CEK APAKAH SCANLOG SUDAH ADA DI TANGGAL TERSEBUT
            $scanlog = Scanlog::where('device_id', $this->device_id)->where(function($query) {
                $query->where(function($query) {
                    $query->whereDate('scan_date', $this->start_date)
                        ->orWhereDate('scan_date', $this->end_date);
                });
            })->whereIn('verify', [1, 2, 3, 4, 6]);

            if($scanlog->exists()){
                $scanlog->delete();
            }

            //GET DATA FROM FINGERSPOT API
            $client = new Client();
            $url = 'https://developer.fingerspot.io/api/get_attlog';

            $body = [
                'trans_id' => '1',
                'cloud_id' => $this->cloud_id,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
            ];

            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer ". env('API_TOKEN_FINGERSPOT'),
            ];

            $response = $client->post($url, [
                'headers' => $headers,
                'json' => $body,
            ]);
            $responseBody = $response->getBody();
            $response = json_decode($responseBody, true);
            $datas = [];
            $pins = [];
            $scanlog_datas = [];

            if(!empty($response)){
                foreach($response['data'] as $data){
                    if(!in_array($data['pin'], $pins)){
                        $pins[] = $data['pin'];
                    }

                    $datas[] = [
                        'pin' => $data['pin'],
                        'scan_date' => $data['scan_date'],
                        'scan_status' => $data['status_scan'],
                        'verify' => $data['verify'],
                        'device_id' => $this->device_id,
                        'organisasi_id' => $this->organisasi_id,
                        'start_date_scan' => $this->start_date,
                        'end_date_scan' => $this->end_date,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                Scanlog::insert($datas);
                activity('success_download_scanlog')->log('Download scanlog from device_id: '.$this->device_id.' start_date: '.$this->start_date.' end_date: '.$this->end_date);
            } else {
                DB::rollBack();
                activity('error_download_scanlog')->log('Error download scanlog - No Response from API');
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();
            activity('error_download_scanlog')->log($e->getMessage());
        }
    }
}
