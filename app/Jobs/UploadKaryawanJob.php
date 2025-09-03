<?php

namespace App\Jobs;

use Throwable;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Posisi;
use App\Models\Karyawan;
use App\Models\Organisasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UploadKaryawanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data, $organisasi_id, $method, $user;
    public $timeout = 1800;
    // public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data, string $organisasi_id, string $method, User $user)
    {
        $this->data = $data;
        $this->organisasi_id = $organisasi_id;
        $this->method = $method;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->method == 'I') {
            $this->processInsert();
        } elseif ($this->method == 'U') {
            $this->processUpdate();
        }
    }

    private function processInsert(): void
    {
        $p = null;
        $row = 0;
        DB::beginTransaction();
        try {
            $karyawans = [];
            $users = [];
            $atasanIds = [];
            $memberIds = [];
            $posisiSync = [];
            $failedData = [];
            foreach ($this->data as $data) {
                $row++;
                if (
                    $data[0] === null || $data[1] === null || $data[3] === null || $data[4] === null || $data[9] === null || $data[10] === null ||
                    $data[12] === null || $data[13] === null || $data[17] === null || $data[18] === null || $data[24] === null || $data[25] === null ||
                     $data[26] === null || $data[27] === null || $data[28] === null || $data[29] === null || $data[31] === null || $data[32] === null ||
                      $data[33] === null || $data[34] === null || $data[35] === null
                ) {
                    $failedData[] = [
                        'row' => $row,
                        'error' => 'Terdapat data yang kosong atau tidak valid pada baris ' . $row
                    ];
                    continue;
                }

                if (!filter_var($data[25], FILTER_VALIDATE_EMAIL)) {
                    $failedData[] = [
                        'row' => $row,
                        'error' => 'Email tidak valid pada baris ' . $row
                    ];
                    continue;
                }

                if (!filter_var($data[27], FILTER_VALIDATE_EMAIL)) {
                    $failedData[] = [
                        'row' => $row,
                        'error' => 'Email perusahaan tidak valid pada baris ' . $row
                    ];
                    continue;
                }

                $posisi = Posisi::where('id_posisi', $data[1])->get()->toArray();
                if (empty($posisi)) {
                    $failedData[] = [
                        'row' => $row,
                        'error' => 'Posisi tidak ditemukan pada baris ' . $row
                    ];
                    continue;
                }

                $ni_karyawan = trim($data[0]);
                $organisasi_id = $this->organisasi_id;
                $nama = $data[3] ?? null;
                $id_karyawan = $this->generateIdKaryawan($nama, $organisasi_id);
                $jenis_kelamin = $data[4] ? strtoupper(trim($data[4])) : null;
                $alamat = $data[5] ?? null;
                $domisili = $data[6] ?? null;
                $tempat_lahir = $data[7] ?? null;
                $tanggal_lahir = $data[8] ? Carbon::createFromFormat('d/m/Y', trim($data[8]))->format('Y-m-d') : null;
                $status_keluarga = $data[9] ? trim($data[9]) : null;
                $kategori_keluarga = $data[10] ? trim($data[10]) : null;
                $agama = $data[11] ? trim($data[11]) : null;
                $no_kk = $data[12] ? trim($data[12]) : null;
                $nik = $data[13] ? trim($data[13]) : null;
                $npwp = $data[14] ? trim($data[14]) : null;
                $no_bpjs_kt = $data[15] ? trim($data[15]) : null;
                $no_bpjs_ks = $data[16] ? trim($data[16]) : null;
                $no_telp = $data[17] ? trim($data[17]) : null;
                $tanggal_mulai = $data[18] ? Carbon::createFromFormat('d/m/Y', trim($data[18]))->format('Y-m-d') : null;
                $jenjang_pendidikan = $data[19] ? preg_replace('/[^A-Za-z0-9]/', '', strtoupper(trim($data[19]))) : null;
                $jurusan_pendidikan = $data[20] ?? null;
                $nama_ibu_kandung = $data[21] ?? null;
                $nama_bank = $data[22] ?? null;
                $nama_rekening = $data[23] ?? null;
                $no_rekening = $data[24] ? trim($data[24]) : null;
                $email = $data[25] ? trim($data[25]) : null;
                $gol_darah = $data[26] ? strtoupper(trim($data[26])) : null;
                $email_perusahaan = $data[27] ? trim($data[27]) : null;
                $username = $data[28] ? trim($data[28]) : null;
                $password = $data[29] ? Hash::make(trim($data[29])) : null;
                $no_telp_darurat = $data[30] ? trim($data[30]) : null;
                $sisa_cuti_pribadi = $data[31] ? trim($data[31]) : 0;
                $sisa_cuti_bersama = $data[32] ? trim($data[32]) : 0;
                $sisa_cuti_tahun_lalu = $data[33] ? trim($data[33]) : 0;
                $expired_date_cuti_tahun_lalu = $data[34] ? Carbon::createFromFormat('d/m/Y', trim($data[34]))->format('Y-m-d') : null;
                $hutang_cuti = $data[35] ? trim($data[35]) : 0;
                $pin = $data[36] ? trim($data[36]) : null;

                $users[] = [
                    'email' => $email_perusahaan,
                    'username' => $username,
                    'password' => $password,
                    'organisasi_id' => $organisasi_id,
                ];

                $karyawans[] = [
                    'organisasi_id' => $organisasi_id,
                    'id_karyawan' => $id_karyawan,
                    'ni_karyawan' => $ni_karyawan,
                    'nama' => $nama,
                    'jenis_kelamin' => $jenis_kelamin,
                    'alamat' => $alamat,
                    'domisili' => $domisili,
                    'tempat_lahir' => $tempat_lahir,
                    'tanggal_lahir' => $tanggal_lahir,
                    'status_keluarga' => $status_keluarga,
                    'kategori_keluarga' => $kategori_keluarga,
                    'agama' => $agama,
                    'no_kk' => $no_kk,
                    'nik' => $nik,
                    'npwp' => $npwp,
                    'no_bpjs_kt' => $no_bpjs_kt,
                    'no_bpjs_ks' => $no_bpjs_ks,
                    'no_telp' => $no_telp,
                    'no_telp_darurat' => $no_telp_darurat,
                    'jenjang_pendidikan' => $jenjang_pendidikan,
                    'jurusan_pendidikan' => $jurusan_pendidikan,
                    'nama_ibu_kandung' => $nama_ibu_kandung,
                    'nama_bank' => $nama_bank,
                    'nama_rekening' => $nama_rekening,
                    'no_rekening' => $no_rekening,
                    'email' => $email,
                    'tanggal_mulai' => $tanggal_mulai,
                    'gol_darah' => $gol_darah,
                    'sisa_cuti_pribadi' => $sisa_cuti_pribadi,
                    'sisa_cuti_bersama' => $sisa_cuti_bersama,
                    'sisa_cuti_tahun_lalu' => $sisa_cuti_tahun_lalu,
                    'expired_date_cuti_tahun_lalu' => $expired_date_cuti_tahun_lalu,
                    'hutang_cuti' => $hutang_cuti,
                    'pin' => $pin,
                ];

                if (!empty($posisi)) {
                    $posisiSync[] = [
                        'karyawan_index' => count($karyawans) - 1,
                        'posisi' => $posisi,
                    ];
                    if ($posisi[0]['jabatan_id'] !== 6) {
                        $atasanIds[] = count($users) - 1;
                    } else {
                        $memberIds[] = count($users) - 1;
                    }
                }
            }

            if(!empty($users)) {
                User::insert($users);
                $newUserIds = User::whereIn('email', collect($users)->pluck('email'))->pluck('id')->toArray();
                foreach ($karyawans as $index => $karyawan) {
                    $karyawans[$index]['user_id'] = $newUserIds[$index] ?? null;
                }

                if (!empty($karyawans)) {
                    Karyawan::insert($karyawans);

                    foreach ($posisiSync as $syncData) {
                        $karyawan = Karyawan::find($karyawans[$syncData['karyawan_index']]['id_karyawan']);
                        $karyawan->posisi()->sync($syncData['posisi'][0]['id_posisi']);
                    }
                }

                $newUsers = User::whereIn('id', $newUserIds)->get();
                foreach ($atasanIds as $indexUser) {
                    $newUsers[$indexUser]->assignRole('atasan');
                }
                foreach ($memberIds as $indexUser) {
                    $newUsers[$indexUser]->assignRole('member');
                }
            }

            if (!empty($failedData)) {
                foreach ($failedData as $data) {
                    activity('error_job_upload_karyawan')
                        ->causedBy($this->user)
                        ->log('Failed upload karyawan - ' . $data['error'] . ' - Baris' . $data['row']);
                }
            }

            activity('job_upload_karyawan')
                ->causedBy($this->user)
                ->log('Upload karyawan - ' . count($karyawans) . ' datas');
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            activity('error_job_upload_karyawan')
                ->causedBy($this->user)
                ->log('Failed upload karyawan -' . $e->getMessage() . ' - Baris' . $row);
        }
    }

    private function processUpdate(): void
    {
        $row = 0;
        DB::beginTransaction();
        try {
            $updatedCount = 0;
            $failedUpdates = [];

            foreach ($this->data as $data) {
                $row++;

                if (empty($data[0])) {
                    $failedUpdates[] = [
                        'row' => $row,
                        'error' => 'NI Karyawan tidak ditemukan pada baris ' . $row . '. Tidak dapat melakukan update.'
                    ];
                    continue;
                }

                $ni_karyawan = trim($data[0]);
                $karyawan = Karyawan::where('ni_karyawan', $ni_karyawan)->first();

                if (!$karyawan) {
                    $failedUpdates[] = [
                        'row' => $row,
                        'error' => 'Karyawan dengan NI ' . $ni_karyawan . ' tidak ditemukan pada baris ' . $row . '. Tidak dapat melakukan update.'
                    ];
                    continue;
                } else {
                    $user = $karyawan->user;
                }

                if (isset($data[1])) {
                    $posisi = Posisi::find($data[1]);
                    if ($posisi) {
                        $karyawan->posisi()->sync($posisi->id_posisi);
                    } else {
                        $failedUpdates[] = [
                            'row' => $row,
                            'error' => 'Posisi dengan ID ' . $data[1] . ' tidak ditemukan pada baris ' . $row . '. Posisi karyawan tidak diupdate.'
                        ];
                        continue;
                    }
                }
                if (isset($data[3])) $karyawan->nama = trim($data[3]);
                if (isset($data[4])) $karyawan->jenis_kelamin = strtoupper(trim($data[4]));
                if (isset($data[5])) $karyawan->alamat = trim($data[5]);
                if (isset($data[6])) $karyawan->domisili = trim($data[6]);
                if (isset($data[7])) $karyawan->tempat_lahir = trim($data[7]);
                if (isset($data[8])) $karyawan->tanggal_lahir = trim($data[8]) ? Carbon::createFromFormat('d/m/Y', trim($data[8]))->format('Y-m-d') : null;
                if (isset($data[9])) $karyawan->status_keluarga = trim($data[9]);
                if (isset($data[10])) $karyawan->kategori_keluarga = trim($data[10]);
                if (isset($data[11])) $karyawan->agama = trim($data[11]);
                if (isset($data[12])) $karyawan->no_kk = trim($data[12]);
                if (isset($data[13])) $karyawan->nik = trim($data[13]);
                if (isset($data[14])) $karyawan->npwp = trim($data[14]);
                if (isset($data[15])) $karyawan->no_bpjs_kt = trim($data[15]);
                if (isset($data[16])) $karyawan->no_bpjs_ks = trim($data[16]);
                if (isset($data[17])) $karyawan->no_telp = trim($data[17]);
                if (isset($data[19])) $karyawan->jenjang_pendidikan = preg_replace('/[^A-Za-z0-9]/', '', strtoupper(trim($data[19])));
                if (isset($data[20])) $karyawan->jurusan_pendidikan = trim($data[20]);
                if (isset($data[21])) $karyawan->nama_ibu_kandung = trim($data[21]);
                if (isset($data[22])) $karyawan->nama_bank = trim($data[22]);
                if (isset($data[23])) $karyawan->nama_rekening = trim($data[23]);
                if (isset($data[24])) $karyawan->no_rekening = trim($data[24]);
                if (isset($data[25])) $karyawan->email = trim($data[25]);
                if (isset($data[26])) $karyawan->gol_darah = strtoupper(trim($data[26]));
                if (isset($data[30])) $karyawan->no_telp_darurat = trim($data[30]);
                if (isset($data[31])) $karyawan->sisa_cuti_pribadi = trim($data[31]);
                if (isset($data[32])) $karyawan->sisa_cuti_bersama = trim($data[32]);
                if (isset($data[33])) $karyawan->sisa_cuti_tahun_lalu = trim($data[33]);
                if (isset($data[34])) $karyawan->expired_date_cuti_tahun_lalu = $data[34] ? Carbon::createFromFormat('d/m/Y', trim($data[34]))->format('Y-m-d') : null;
                if (isset($data[35])) $karyawan->hutang_cuti = trim($data[35]);
                if (isset($data[36])) $karyawan->pin = trim($data[36]);

                // if (isset($data[27])) $user->email = trim($data[27]);
                // if (isset($data[28])) $user->username = trim($data[28]);
                // if (isset($data[29])) $user->password = Hash::make(trim($data[29]));

                // if ($user->isDirty()) {
                //     $user->save();
                // }

                if ($karyawan->isDirty()) {
                    $karyawan->save();
                    $updatedCount++;
                }
            }

            if (!empty($failedUpdates)) {
                foreach ($failedUpdates as $data) {
                    activity('error_job_upload_karyawan')
                        ->causedBy($this->user)
                        ->log('Failed update karyawan - ' . $data['error'] . ' - Baris: ' . $data['row']);
                }
            }

            activity('job_update_karyawan')
                ->causedBy($this->user)
                ->log('Update karyawan - ' . $updatedCount . ' data berhasil diupdate.');

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            activity('error_job_upload_karyawan')
                ->causedBy($this->user)
                ->log('Failed update karyawan - ' . $e->getMessage() . ' - Baris: ' . $row);
        }
    }

    function generateIdKaryawan($name, $organisasi_id)
    {
        $organisasi = Organisasi::find($organisasi_id)->nama;

        if ($organisasi) {
            $organisasi = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($organisasi));
        } else {
            $organisasi = 'KRY';
        }

        $words = explode(' ', $name);

        if (count($words) === 1) {
            $initials = substr($name, 0, 2);
        } else {
            $initials = substr($words[0], 0, 1) . substr($words[1], 0, 1);
        }

        $timestamp = now()->timestamp;
        $baseString = $organisasi.'-'.$initials . $timestamp . rand(100, 999);

        return $baseString;
    }
}
