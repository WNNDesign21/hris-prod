<?php

namespace App\Jobs;

use Throwable;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Posisi;
use App\Models\Karyawan;
use App\Models\Organisasi;
use App\Models\KeluargaKaryawan;
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
            $keluargaKaryawans = [];

            foreach ($this->data as $data) {
                $row++;
                if (
                    empty($data[0]) || empty($data[1]) || empty($data[3]) || empty($data[4]) || empty($data[9]) ||
                    empty($data[13]) || empty($data[18]) || empty($data[25]) || empty($data[27]) || empty($data[28]) ||
                    empty($data[29]) || !isset($data[31]) || !isset($data[32]) || !isset($data[33]) || empty($data[34]) ||
                    !isset($data[35]) || empty($data[36]) || empty($data[37]) || empty($data[38])
                ) {
                    $failedData[] = [
                        'row' => $row,
                        'error' => 'Terdapat data yang kosong atau tidak valid pada baris ' . $row
                    ];
                    continue;
                }

                if (!filter_var(trim($data[25]), FILTER_VALIDATE_EMAIL)) {
                    $failedData[] = [
                        'row' => $row,
                        'error' => 'Email pribadi tidak valid pada baris ' . $row
                    ];
                    continue;
                }

                if (!filter_var(trim($data[27]), FILTER_VALIDATE_EMAIL)) {
                    $failedData[] = [
                        'row' => $row,
                        'error' => 'Email perusahaan tidak valid pada baris ' . $row
                    ];
                    continue;
                }

                $posisi = Posisi::where('id_posisi', trim($data[1]))->get()->toArray();
                if (empty($posisi)) {
                    $failedData[] = [
                        'row' => $row,
                        'error' => 'Posisi tidak ditemukan pada baris ' . $row
                    ];
                    continue;
                }

                $ni_karyawan = trim($data[0]);
                $organisasi_id = $this->organisasi_id;
                $nama = trim($data[3]) ?? null;
                $id_karyawan = $this->generateIdKaryawan($nama, $organisasi_id);
                $jenis_kelamin = !empty($data[4]) ? strtoupper(trim($data[4])) : null;
                $alamat = !empty($data[5]) ? trim($data[5]) : null;
                $domisili = !empty($data[6]) ? trim($data[6]) : null;
                $tempat_lahir = !empty($data[7]) ? trim($data[7]) : null;
                $tanggal_lahir = !empty($data[8]) ? Carbon::createFromFormat('d/m/Y', trim($data[8]))->format('Y-m-d') : null;
                $status_keluarga = !empty($data[9]) ? trim($data[9]) : null;
                $kategori_keluarga = !empty($data[10]) ? trim($data[10]) : null;
                $agama = !empty($data[11]) ? trim($data[11]) : null;
                $no_kk = !empty($data[12]) ? trim($data[12]) : null;
                $nik = !empty($data[13]) ? trim($data[13]) : null;
                $npwp = !empty($data[14]) ? trim($data[14]) : null;
                $no_bpjs_kt = !empty($data[15]) ? trim($data[15]) : null;
                $no_bpjs_ks = !empty($data[16]) ? trim($data[16]) : null;
                $no_telp = !empty($data[17]) ? trim($data[17]) : null;
                $tanggal_mulai = !empty($data[18]) ? Carbon::createFromFormat('d/m/Y', trim($data[18]))->format('Y-m-d') : null;
                $jenjang_pendidikan = !empty($data[19]) ? preg_replace('/[^A-Za-z0-9]/', '', strtoupper(trim($data[19]))) : null;
                $jurusan_pendidikan = !empty($data[20]) ? trim($data[20]) : null;
                $nama_ibu_kandung = !empty($data[21]) ? trim($data[21]) : null;
                $nama_bank = !empty($data[22]) ? trim($data[22]) : null;
                $nama_rekening = !empty($data[23]) ? trim($data[23]) : null;
                $no_rekening = !empty($data[24]) ? trim($data[24]) : null;
                $email = !empty($data[25]) ? trim($data[25]) : null;
                $gol_darah = !empty($data[26]) ? strtoupper(trim($data[26])) : null;
                $email_perusahaan = !empty($data[27]) ? trim($data[27]) : null;
                $username = !empty($data[28]) ? trim($data[28]) : null;
                $password = !empty($data[29]) ? Hash::make(trim($data[29])) : null;
                $no_telp_darurat = !empty($data[30]) ? trim($data[30]) : null;
                $sisa_cuti_pribadi = isset($data[31]) ? trim($data[31]) : 0;
                $sisa_cuti_bersama = isset($data[32]) ? trim($data[32]) : 0;
                $sisa_cuti_tahun_lalu = isset($data[33]) ? trim($data[33]) : 0;
                $expired_date_cuti_tahun_lalu = !empty($data[34]) ? Carbon::createFromFormat('d/m/Y', trim($data[34]))->format('Y-m-d') : null;
                $hutang_cuti = isset($data[35]) ? trim($data[35]) : 0;
                $pin = !empty($data[36]) ? trim($data[36]) : null;
                $tipe_karyawan = !empty($data[37]) ? trim($data[37]) : null;
                $direct = ($tipe_karyawan == 'D') ? 1 : null;
                $indirect = ($tipe_karyawan == 'I') ? 1 : null;
                $status_kawin = !empty($data[38]) ? trim($data[38]) : null;

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
                    'direct' => $direct,
                    'indirect' => $indirect,
                    'status_kawin' => $status_kawin,
                ];

                if (str_starts_with(strtoupper($status_kawin), 'K')) {
                    // Process spouse data
                    if (!empty($data[39]) && !empty($data[40]) && !empty($data[41]) && !empty($data[42])) {
                        $keluargaKaryawans[] = [
                            'karyawan_id' => $id_karyawan,
                            'hubungan' => trim($data[39]),
                            'nama' => trim($data[40]),
                            'tempat_lahir' => trim($data[41]),
                            'tanggal_lahir' => Carbon::createFromFormat('d/m/Y', trim($data[42]))->format('Y-m-d'),
                        ];
                    }

                    // Process children data
                    if (str_starts_with(strtoupper($status_kawin), 'KA')) {
                        $anakCount = (int) substr(strtoupper($status_kawin), 2);
                        if ($anakCount > 0) {
                            for ($j = 0; $j < $anakCount; $j++) {
                                $i = 43 + ($j * 4);
                                if (!empty($data[$i]) && !empty($data[$i+1]) && !empty($data[$i+2]) && !empty($data[$i+3])) {
                                    $keluargaKaryawans[] = [
                                        'karyawan_id' => $id_karyawan,
                                        'hubungan' => trim($data[$i]),
                                        'nama' => trim($data[$i + 1]),
                                        'tempat_lahir' => trim($data[$i + 2]),
                                        'tanggal_lahir' => Carbon::createFromFormat('d/m/Y', trim($data[$i + 3]))->format('Y-m-d'),
                                    ];
                                }
                            }
                        }
                    }
                }

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

            if (!empty($users)) {
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

                if (!empty($keluargaKaryawans)) {
                    KeluargaKaryawan::insert($keluargaKaryawans);
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
                if (!empty($data[3])) $karyawan->nama = trim($data[3]);
                if (!empty($data[4])) $karyawan->jenis_kelamin = strtoupper(trim($data[4]));
                if (!empty($data[5])) $karyawan->alamat = trim($data[5]);
                if (!empty($data[6])) $karyawan->domisili = trim($data[6]);
                if (!empty($data[7])) $karyawan->tempat_lahir = trim($data[7]);
                if (!empty($data[8])) $karyawan->tanggal_lahir = trim($data[8]) ? Carbon::createFromFormat('d/m/Y', trim($data[8]))->format('Y-m-d') : null;
                if (!empty($data[9])) $karyawan->status_keluarga = trim($data[9]);
                if (!empty($data[10])) $karyawan->kategori_keluarga = trim($data[10]);
                if (!empty($data[11])) $karyawan->agama = trim($data[11]);
                if (!empty($data[12])) $karyawan->no_kk = trim($data[12]);
                if (!empty($data[13])) $karyawan->nik = trim($data[13]);
                if (!empty($data[14])) $karyawan->npwp = trim($data[14]);
                if (!empty($data[15])) $karyawan->no_bpjs_kt = trim($data[15]);
                if (!empty($data[16])) $karyawan->no_bpjs_ks = trim($data[16]);
                if (!empty($data[17])) $karyawan->no_telp = trim($data[17]);
                if (!empty($data[19])) $karyawan->jenjang_pendidikan = preg_replace('/[^A-Za-z0-9]/', '', strtoupper(trim($data[19])));
                if (!empty($data[20])) $karyawan->jurusan_pendidikan = trim($data[20]);
                if (!empty($data[21])) $karyawan->nama_ibu_kandung = trim($data[21]);
                if (!empty($data[22])) $karyawan->nama_bank = trim($data[22]);
                if (!empty($data[23])) $karyawan->nama_rekening = trim($data[23]);
                if (!empty($data[24])) $karyawan->no_rekening = trim($data[24]);
                if (!empty($data[25])) $karyawan->email = trim($data[25]);
                if (!empty($data[26])) $karyawan->gol_darah = strtoupper(trim($data[26]));
                if (!empty($data[30])) $karyawan->no_telp_darurat = trim($data[30]);
                if (isset($data[31])) $karyawan->sisa_cuti_pribadi = trim($data[31]);
                if (isset($data[32])) $karyawan->sisa_cuti_bersama = trim($data[32]);
                if (isset($data[33])) $karyawan->sisa_cuti_tahun_lalu = trim($data[33]);
                if (!empty($data[34])) $karyawan->expired_date_cuti_tahun_lalu = $data[34] ? Carbon::createFromFormat('d/m/Y', trim($data[34]))->format('Y-m-d') : null;
                if (isset($data[35])) $karyawan->hutang_cuti = trim($data[35]);
                if (!empty($data[36])) $karyawan->pin = trim($data[36]);
                if (!empty($data[37])) {
                    $tipe_karyawan = trim($data[37]);
                    $karyawan->direct = ($tipe_karyawan == 'D') ? 1 : null;
                    $karyawan->indirect = ($tipe_karyawan == 'I') ? 1 : null;
                }
                if (!empty($data[38])) $karyawan->status_kawin = trim($data[38]);

                if ($karyawan->status_kawin) {
                    if (str_starts_with(strtoupper($karyawan->status_kawin), 'K')) {
                        // Process spouse data
                        if (!empty($data[39]) && !empty($data[40]) && !empty($data[41]) && !empty($data[42])) {
                            KeluargaKaryawan::updateOrCreate(
                                ['karyawan_id' => $karyawan->id_karyawan, 'hubungan' => trim($data[39])],
                                [
                                    'nama' => trim($data[40]),
                                    'tempat_lahir' => trim($data[41]),
                                    'tanggal_lahir' => Carbon::createFromFormat('d/m/Y', trim($data[42]))->format('Y-m-d'),
                                ]
                            );
                        }

                        // Process children data
                        if (str_starts_with(strtoupper($karyawan->status_kawin), 'KA')) {
                            $anakCount = (int) substr(strtoupper($karyawan->status_kawin), 2);
                            if ($anakCount > 0) {
                                for ($j = 0; $j < $anakCount; $j++) {
                                    $i = 43 + ($j * 4);
                                    if (!empty($data[$i]) && !empty($data[$i+1]) && !empty($data[$i+2]) && !empty($data[$i+3])) {
                                        KeluargaKaryawan::updateOrCreate(
                                            ['karyawan_id' => $karyawan->id_karyawan, 'nama' => trim($data[$i + 1])],
                                            [
                                                'hubungan' => trim($data[$i]),
                                                'tempat_lahir' => trim($data[$i + 2]),
                                                'tanggal_lahir' => Carbon::createFromFormat('d/m/Y', trim($data[$i + 3]))->format('Y-m-d'),
                                            ]
                                        );
                                    }
                                }
                            }
                        }
                    }
                }

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