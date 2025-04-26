<?php

namespace App\Jobs;

use Exception;
use Throwable;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\Posisi;
use App\Models\Kontrak;
use App\Models\Karyawan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UploadKontrakJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data, $organisasi_id, $user;
    public $timeout = 1800;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data, int $organisasi_id, User $user)
    {
        $this->data = $data;
        $this->organisasi_id = $organisasi_id;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $row = 0;
        DB::beginTransaction();
        try {
            $kontraks = [];
            $karyawans = [];
            $karyawanIds = [];
            $failedDatas = [];

            foreach ($this->data as $index => $item) {
                $row++;

                if ($item[0] == null || $item[2] == null || $item[4] == null || $item[5] == null
                    || $item[6] == null || $item[7] == null || $item[8] == null || $item[9] == null
                    || $item[10] == null
                ) {
                    $failedDatas[] = [
                        'row' => $row,
                        'error' => 'Terdapat data yang kosong atau tidak valid pada baris ' . $row
                    ];
                    continue;
                }

                // //Validasi Nomor Induk Karyawan
                $karyawan = Karyawan::where('ni_karyawan', $item[0])->first();
                if(!isset($karyawan->id_karyawan)){
                    $failedDatas[] = [
                        'row' => $row,
                        'error' => 'Karyawan dengan Nomor Induk ' . $item[0] . ' tidak ditemukan!'
                    ];
                    continue;
                } else {
                    $karyawanIds[] = $karyawan->id_karyawan;
                }

                // Validasi Jenis Kontrak
                if (!in_array($item[5], ['PKWT', 'PKWTT', 'MAGANG'])) {
                    $failedDatas[] = [
                        'row' => $row,
                        'error' => 'Jenis kontrak pada baris ' . $row . ' harus PKWT, PKWTT, atau MAGANG!'
                    ];
                    continue;
                }

                //Validasi Kolom Numeric
                if (!is_numeric($item[2]) || !is_numeric($item[6]) || $item[6] < 0 || !is_numeric($item[7]) || $item[7] < 0) {
                    $failedDatas[] = [
                        'row' => $row,
                        'error' => 'Kolom numeric pada baris ' . $row . ' tidak valid!'
                    ];
                    continue;
                }

                $posisi = Posisi::where('id_posisi', $item[2])->first();
                if(!$posisi){
                    $failedDatas[] = [
                        'row' => $row,
                        'error' => 'Posisi dengan ID ' . $item[2] . ' tidak ditemukan!'
                    ];
                    continue;
                }

                $tanggal_mulai = Carbon::createFromFormat('d/m/Y', $item[8])->format('Y-m-d');
                if($item[5] !== 'PKWTT'){
                    $tanggal_selesai = Carbon::createFromFormat('d/m/Y', $item[9])->format('Y-m-d');
                    if($karyawan->tanggal_selesai < $tanggal_selesai || $karyawan->tanggal_selesai == null){
                        $tanggal_selesai = Carbon::createFromFormat('d/m/Y', $item[9])->format('Y-m-d');
                    } else {
                        $tanggal_selesai = $karyawan->tanggal_selesai;
                    }
                } else {
                    $tanggal_selesai = null;
                }

                if ($item[5] !== 'PKWTT') {
                    $existingKontrak = Kontrak::where('karyawan_id', $karyawan->id_karyawan)
                        ->where('status', 'DONE')
                        ->where('jenis', '!=', 'PKWTT')
                        ->where(function ($query) use ($tanggal_mulai, $tanggal_selesai) {
                            $query->whereBetween('tanggal_mulai', [$tanggal_mulai, $tanggal_selesai])
                                  ->orWhereBetween('tanggal_selesai', [$tanggal_mulai, $tanggal_selesai])
                                  ->orWhere(function ($subQuery) use ($tanggal_mulai, $tanggal_selesai) {
                                      $subQuery->where('tanggal_mulai', '<=', $tanggal_mulai)
                                               ->where('tanggal_selesai', '>=', $tanggal_selesai);
                                  });
                        })
                        ->exists();

                    if ($existingKontrak) {
                        $failedDatas[] = [
                            'row' => $row,
                            'error' => 'Karyawan dengan Nomor Induk ' . $item[0] . ' sudah memiliki kontrak pada periode yang sama!'
                        ];
                        continue;
                    }
                }

                $karyawans[$index] = [
                    'id_karyawan' => $karyawanIds[$index],
                    'jenis_kontrak' => $item[5],
                    'status_karyawan' => 'AT',
                    'tanggal_selesai' => $tanggal_selesai,
                ];

                $kontraks[] = [
                    'no_surat' => $item[4],
                    'id_kontrak' => 'KONTRAK-'. Str::random(4) . '-' . now()->timestamp,
                    'karyawan_id' =>  $karyawan->id_karyawan,
                    'posisi_id' => $posisi->id_posisi,
                    'nama_posisi' => $posisi->nama,
                    'jenis' => trim($item[5]),
                    'status' => 'DONE',
                    'durasi' => trim($item[5]) !== 'PKWTT' ? trim($item[6]) : null,
                    'salary' => trim($item[7]),
                    'deskripsi' => 'History Kontrak Karyawan',
                    'tanggal_mulai' => $tanggal_mulai,
                    'tanggal_selesai' => trim($item[5]) !== 'PKWTT' ? $tanggal_selesai : null,
                    'tempat_administrasi' => $item[10],
                    'isReactive' => 'N',
                    'organisasi_id' => $this->organisasi_id,
                    'issued_date' => Carbon::now()->format('Y-m-d'),
                ];
            }

            //Update Karyawan
            if (!empty($karyawans)) {
                Karyawan::upsert($karyawans, ['id_karyawan'], [
                    'jenis_kontrak',
                    'status_karyawan',
                    'tanggal_selesai',
                ]);
            }

            if (!empty($kontraks)) {
                Kontrak::upsert($kontraks, ['id_kontrak'], [
                    'no_surat',
                    'karyawan_id',
                    'posisi_id',
                    'nama_posisi',
                    'jenis',
                    'status',
                    'durasi',
                    'salary',
                    'deskripsi',
                    'tanggal_mulai',
                    'tanggal_selesai',
                    'tempat_administrasi',
                    'isReactive',
                    'organisasi_id',
                    'issued_date'
                ]);
            }

            //Update sisa cuti bersama karyawan
            if (!empty($karyawanIds)) {
                $array_karyawan = array_unique($karyawanIds);
                foreach ($array_karyawan as $index => $kry){
                    $k = Karyawan::find($kry);
                    //CEK APAKAH ADA CUTI BERSAMA SEBELUM TANGGAL SELESAI KONTRAK YANG BARU DI UPLOAD
                    if($k && $k->tanggal_selesai !== null && $k->jenis_kontrak !== 'PKWTT'){
                        $kontrak = Kontrak::where('karyawan_id', $kry)->where('status', 'DONE')->orderBy('tanggal_selesai', 'DESC')->first();
                        $existingCB = Event::whereDate('tanggal_mulai', '<=', $kontrak->tanggal_selesai)->where('jenis_event', 'CB');
                        if($existingCB->exists()){
                            foreach($existingCB->get() as $cutiBersama){
                                $jatah_cuti_bersama = $k->sisa_cuti_bersama - $cutiBersama->durasi;
                                if($jatah_cuti_bersama >= 0){
                                    $k->sisa_cuti_bersama = $jatah_cuti_bersama;
                                    $k->save();
                                } else {
                                    $k->sisa_cuti_bersama = 0;
                                    $k->hutang_cuti = abs($jatah_cuti_bersama);
                                    $k->save();
                                }
                            }
                        }
                    } elseif ($k && $k->jenis_kontrak == 'PKWTT'){
                        $kontrak = Kontrak::where('karyawan_id', $kry)->where('status', 'DONE')->where('jenis', 'PKWTT')->orderBy('tanggal_mulai', 'DESC')->first();
                        $tanggal_selesai_temp = Carbon::now()->year.'-'.Carbon::parse($kontrak->tanggal_mulai)->format('m-d');
                        $existingCB = Event::whereDate('tanggal_mulai', '>=', $tanggal_selesai_temp)->where('jenis_event', 'CB');
                        if($existingCB->exists()){
                            foreach($existingCB->get() as $cutiBersama){
                                $jatah_cuti_bersama = $k->sisa_cuti_bersama - $cutiBersama->durasi;
                                if($jatah_cuti_bersama >= 0){
                                    $k->sisa_cuti_bersama = $jatah_cuti_bersama;
                                    $k->save();
                                } else {
                                    $k->sisa_cuti_bersama = 0;
                                    $k->hutang_cuti = abs($jatah_cuti_bersama);
                                    $k->save();
                                }
                            }
                        }
                    }
                }
            } else {
                $failedDatas[] = [
                    'row' => $row,
                    'error' => 'Tidak ada data karyawan yang valid untuk diupdate!'
                ];
            }

            if (!empty($failedDatas)) {
                foreach ($failedDatas as $item) {
                    activity('error_job_upload_kontrak')
                        ->causedBy($this->user)
                        ->log('Failed upload kontrak - ' . $item['error'] . ' - Baris' . $item['row']);
                }
            }

            activity('job_upload_kontrak')
                ->causedBy($this->user)
                ->log('Upload kontrak - ' . count($kontraks) . ' datas');
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            activity('error_job_upload_kontrak')
                ->causedBy($this->user)
                ->log('Failed upload kontrak -' . $e->getMessage() . ' - Baris' . $row);
        }
    }
}
