<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Kontrak;
use App\Models\Karyawan;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $karyawan = [
            [
                'id_karyawan' => 'FL96485264',
                'ni_karyawan' => '300.010224',
                'nik' => '32150116274715',
                'no_kk' => '3215011620002',
                'nama' => 'FLAVIA DOMITELA AJENG NARISWARI',
                'jenis_kelamin' => 'P',
                'jenis_kontrak' => 'PKWT',
                'status_karyawan' => 'AKTIF',
                'status_keluarga' => 'MENIKAH',
                'kategori_keluarga' => 'K1',
                'tempat_lahir' => 'Purwakarta',
                'tanggal_lahir' => '1999-02-15',
                'alamat' => 'Jl.Ninjaku No.22',
                'domisili' => 'Jl.Ninjaku No.22',
                'agama' => 'KRISTEN',
                'gol_darah' => 'O',
                'npwp' => '3251.5448.6666',
                'no_bpjs_ks' => '7845997477',
                'no_bpjs_kt' => '7845994537',
                'email' => 'fl@gmail.com',
                'no_telp' => '08987335244',
                'no_telp_darurat' => '08987335212',
                'no_rekening' => '171752162',
                'nama_rekening' => 'FLAVIA DOMITELA',
                'nama_bank' => 'MANDIRI',
                'nama_ibu_kandung' => 'AJENG',
                'jenjang_pendidikan' => 'S1',
                'jurusan_pendidikan' => 'Psikologi',
                'sisa_cuti' => 12,
                'tanggal_mulai' => Carbon::now()->toDateString(),
                'tanggal_selesai' => Carbon::now()->addMonths(1)->toDateString(),
                'user_id' => 3,
                'grup_id' => 1
            ],
            [
                'id_karyawan' => 'IN16324875',
                'ni_karyawan' => '200.100224',
                'nik' => '32150116274522',
                'no_kk' => '3215011620018',
                'nama' => 'INDAH NADIA HAPSARI',
                'jenis_kelamin' => 'P',
                'jenis_kontrak' => 'PKWT',
                'status_karyawan' => 'AKTIF',
                'status_keluarga' => 'MENIKAH',
                'kategori_keluarga' => 'K2',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1989-11-05',
                'alamat' => 'Jl.Raimuas No.99',
                'domisili' => 'Jl.Raimuas No.99',
                'agama' => 'ISLAM',
                'gol_darah' => 'O',
                'npwp' => '3251.5448.6777',
                'no_bpjs_ks' => '8045997477',
                'no_bpjs_kt' => '8045994537',
                'email' => 'in@gmail.com',
                'no_telp' => '08987344244',
                'no_telp_darurat' => '08987335233',
                'no_rekening' => '181752162',
                'nama_rekening' => 'INDAH NADIA',
                'nama_bank' => 'MANDIRI',
                'nama_ibu_kandung' => 'NADIA PUTRI',
                'jenjang_pendidikan' => 'SMA',
                'jurusan_pendidikan' => 'IPA',
                'sisa_cuti' => 12,
                'tanggal_mulai' => Carbon::now()->toDateString(),
                'tanggal_selesai' => Carbon::now()->addMonths(1)->toDateString(),
                'user_id' => 4,
                'grup_id' => 1
            ],
            [
                'id_karyawan' => 'AM15687542',
                'ni_karyawan' => '479.030624',
                'nik' => '32150116274999',
                'no_kk' => '3215011620059',
                'nama' => 'AMBAR WINASTI',
                'jenis_kelamin' => 'P',
                'jenis_kontrak' => 'PKWT',
                'status_karyawan' => 'AKTIF',
                'status_keluarga' => 'MENIKAH',
                'kategori_keluarga' => 'K2',
                'tempat_lahir' => 'Karawang',
                'tanggal_lahir' => '1985-12-10',
                'alamat' => 'Jl.Hareddd No.05',
                'domisili' => 'Jl.Hareddd No.05',
                'agama' => 'ISLAM',
                'gol_darah' => 'O',
                'npwp' => '2251.5448.6777',
                'no_bpjs_ks' => '9045997477',
                'no_bpjs_kt' => '9045994537',
                'email' => 'am@gmail.com',
                'no_telp' => '08777344222',
                'no_telp_darurat' => '0898739090',
                'no_rekening' => '191752162',
                'nama_rekening' => 'AMBAR WINASTI',
                'nama_bank' => 'MANDIRI',
                'nama_ibu_kandung' => 'WINASTI DRAJAT',
                'jenjang_pendidikan' => 'S1',
                'jurusan_pendidikan' => 'Manajemen',
                'sisa_cuti' => 12,
                'tanggal_mulai' => Carbon::now()->toDateString(),
                'tanggal_selesai' => Carbon::now()->addMonths(1)->toDateString(),
                'user_id' => 5,
                'grup_id' => 1
            ],
            [
                'id_karyawan' => 'FA95453155',
                'ni_karyawan' => '480.030324',
                'nik' => '32150116200045',
                'no_kk' => '3215011620017',
                'nama' => 'FATHAN PEBRILLIESTYO RIDWAN',
                'jenis_kelamin' => 'L',
                'jenis_kontrak' => 'PKWT',
                'status_karyawan' => 'AKTIF',
                'status_keluarga' => 'BELUM MENIKAH',
                'kategori_keluarga' => 'TK0',
                'tempat_lahir' => 'Karawang',
                'tanggal_lahir' => '2002-02-17',
                'alamat' => 'Jl.Sikasep No.17',
                'domisili' => 'Jl.Sikasep No.17',
                'agama' => 'ISLAM',
                'gol_darah' => 'O',
                'npwp' => '2251.5448.6171',
                'no_bpjs_ks' => '9045997417',
                'no_bpjs_kt' => '9045994517',
                'email' => 'fa@gmail.com',
                'no_telp' => '08777344217',
                'no_telp_darurat' => '0898739017',
                'no_rekening' => '191752117',
                'nama_rekening' => 'FATHAN PEBRI',
                'nama_bank' => 'MANDIRI',
                'nama_ibu_kandung' => 'LILIS SULIS',
                'jenjang_pendidikan' => 'S1',
                'jurusan_pendidikan' => 'Informatika',
                'sisa_cuti' => 12,
                'tanggal_mulai' => Carbon::now()->toDateString(),
                'tanggal_selesai' => Carbon::now()->addMonths(1)->toDateString(),
                'user_id' => 6,
                'grup_id' => 1
            ],
        ];

        foreach ($karyawan as $kry) {
            Karyawan::create($kry);
        }

        $karyawans = Karyawan::all();

        $i = 1;
        foreach ($karyawans as $kry) {
            Kontrak::create([
                'no_surat' => 'No. 001/PKWT-I/HRD-TCF3/2024',
                'id_kontrak' => 'KONTRAK-'. Str::random(4) . '-' . now()->timestamp,
                'karyawan_id' =>  $kry->id_karyawan,
                'nama_posisi' => 'Initial Document',
                'jenis' => 'PKWT',
                'status' => 'DONE',
                'durasi' => 1,
                'salary' => 0,
                'deskripsi' => 'Initial Contract for generate Tanggal Mulai dan Tanggal Akhir',
                'tanggal_mulai' => Carbon::now()->toDateString(),
                'tanggal_selesai' => Carbon::now()->addMonths(1)->toDateString(),
                'isReactive' => 'N',
            ]);
            $i++;
        }

    }
}
