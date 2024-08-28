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
                'id_karyawan' => 'FL0001',
                'nama' => 'FLAVIA DOMITELA AJENG NARISWARI',
                'jenis_kontrak' => 'PKWT',
                'status_karyawan' => 'AKTIF',
                'sisa_cuti' => 12,
                'tanggal_mulai' => Carbon::now()->toDateString(),
                'tanggal_selesai' => Carbon::now()->addMonths(6)->toDateString(),
                'user_id' => 1,
                'grup_id' => 1
            ],
            [
                'id_karyawan' => 'IN0001',
                'nama' => 'INDAH NADIA HAPSARI',
                'jenis_kontrak' => 'MAGANG',
                'status_karyawan' => 'AKTIF',
                'sisa_cuti' => 12,
                'tanggal_mulai' => Carbon::now()->toDateString(),
                'tanggal_selesai' => Carbon::now()->addMonths(6)->toDateString(),
                'user_id' => 2,
                'grup_id' => 1
            ],
            [
                'id_karyawan' => 'AM0001',
                'nama' => 'AMBAR WINASTI',
                'jenis_kontrak' => 'PKWTT',
                'status_karyawan' => 'AKTIF',
                'sisa_cuti' => 12,
                'tanggal_mulai' => Carbon::now()->toDateString(),
                'tanggal_selesai' => Carbon::now()->addMonths(6)->toDateString(),
                'user_id' => 3,
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
                'id_kontrak' => 'KONTRAK-'. Str::random(4) . '-' . now()->timestamp,
                'karyawan_id' =>  $kry->id_karyawan,
                'nama_posisi' => 'Posisi '. $i,
                'jenis' => 'PKWT',
                'status' => 'DONE',
                'durasi' => 6,
                'salary' => 5250000,
                'deskripsi' => 'Potongan A = 3% , Potongan B = 1.5%, Potongan C = 1%',
                'tanggal_mulai' => Carbon::now()->toDateString(),
                'tanggal_selesai' => Carbon::now()->addMonths(6)->toDateString(),
                'isReactive' => 'N',
            ]);
            $i++;
        }
    }
}
