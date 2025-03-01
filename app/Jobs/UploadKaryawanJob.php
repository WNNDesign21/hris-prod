<?php

namespace App\Jobs;

use Throwable;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Posisi;
use App\Models\Karyawan;
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

    public $data, $organisasi_id;
    public $timeout = 1800;
    // public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data, string $organisasi_id)
    {
        $this->data = $data;
        $this->organisasi_id = $organisasi_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();
        $p = null;
        $row = 0;
        try {
            $karyawans = [];
            $users = [];
            $atasanIds = [];
            $memberIds = [];
            $posisiSync = [];
            foreach ($this->data as $data) {
                $row++;
                $ni_karyawan = trim($data[0]);
                $organisasi_id = $this->organisasi_id;
                $nama = $data[2] ?? null;
                $id_karyawan = $this->generateIdKaryawan($nama, $organisasi_id);
                $jenis_kelamin = $data[3] ? strtoupper(trim($data[3])) : null;
                $alamat = $data[4] ?? null;
                $domisili = $data[5] ?? null;
                $tempat_lahir = $data[6] ?? null;
                $tanggal_lahir = $data[7] ? Carbon::createFromFormat('d/m/Y', trim($data[7]))->format('Y-m-d') : null;
                $status_keluarga = $data[8] ? trim($data[8]) : null;
                $kategori_keluarga = $data[9] ? trim($data[9]) : null;
                $agama = $data[10] ? trim($data[10]) : null;
                $no_kk = $data[11] ? trim($data[11]) : null;
                $nik = $data[12] ? trim($data[12]) : null;
                $npwp = $data[13] ? trim($data[13]) : null;
                $no_bpjs_kt = $data[14] ? trim($data[14]) : null;
                $no_bpjs_ks = $data[15] ? trim($data[15]) : null;
                $no_telp = $data[16] ? trim($data[16]) : null;
                $tanggal_mulai = $data[17] ? Carbon::createFromFormat('d/m/Y', trim($data[17]))->format('Y-m-d') : null;
                $jenjang_pendidikan = $data[18] ? preg_replace('/[^A-Za-z0-9]/', '', strtoupper(trim($data[18]))) : null;
                $jurusan_pendidikan = $data[19] ?? null;
                $nama_ibu_kandung = $data[20] ?? null;
                $nama_bank = $data[21] ?? null;
                $nama_rekening = $data[22] ?? null;
                $no_rekening = $data[23] ? trim($data[23]) : null;
                $email = $data[24] ? filter_var(trim($data[24]), FILTER_VALIDATE_EMAIL) : null;
                $gol_darah = $data[25] ? strtoupper(trim($data[25])) : null;
                $email_perusahaan = $data[26] ? filter_var(trim($data[26]), FILTER_VALIDATE_EMAIL) : null;
                $username = $data[27] ? trim($data[27]) : null;
                $password = $data[28] ? Hash::make(trim($data[28])) : null;
                $no_telp_darurat = $data[29] ? trim($data[29]) : null;
                $sisa_cuti_pribadi = $data[30] ? trim($data[30]) : 0;
                $sisa_cuti_bersama = $data[31] ? trim($data[31]) : 0;
                $sisa_cuti_tahun_lalu = $data[32] ? trim($data[32]) : 0;
                $expired_date_cuti_tahun_lalu = $data[33] ? Carbon::createFromFormat('d/m/Y', trim($data[33]))->format('Y-m-d') : null;
                $hutang_cuti = $data[34] ? trim($data[34]) : 0;

                $posisi = $data[1] ? Posisi::where('nama', $data[1])->get()->toArray() : [];
                $p = $posisi;
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
                    'status_karyawan' => 'AT',
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

            User::insert($users);
            $userIds = DB::getPdo()->lastInsertId();
            foreach($karyawans as $index => $karyawan){
                $karyawans[$index]['user_id'] = $userIds - count($karyawans) + $index +1;
            }

            Karyawan::insert($karyawans);

            foreach ($posisiSync as $syncData) {
                $karyawan = Karyawan::find($karyawans[$syncData['karyawan_index']]['id_karyawan']);
                $karyawan->posisi()->sync($syncData['posisi'][0]['id_posisi']);
            }
            
            $newUserIds = range($userIds - count($users) + 1, $userIds);
            $newUsers = User::whereIn('id', $newUserIds)->get();
            
            foreach ($atasanIds as $indexUser){
                $newUsers[$indexUser]->assignRole('atasan');
            }
            foreach ($memberIds as $indexUser){
                $newUsers[$indexUser]->assignRole('member');
            }

            activity('job_upload_karyawan')
                ->causedBy(auth()->user())
                ->log('Upload karyawan - ' . count($karyawans) . ' datas');
            DB:: commit();
        } catch (Throwable $e) {
            DB::rollBack();
            activity('job_upload_karyawan')
                    ->causedBy(auth()->user())
                    ->log('Failed upload karyawan -'. $e->getMessage() . ' - ' . $row);
        }
    }

    function generateIdKaryawan($name, $organisasi_id)
    {
        if($organisasi_id == '1'){
            $organisasi = 'TCF2';
        } else {
            $organisasi = 'TCF3';
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
