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
                'nama' => 'FLAVIA DOMITELA AJENG NARISWARI',
                'jenis_kelamin' => 'P',
                'jenis_kontrak' => 'PKWT',
                'status_karyawan' => 'AKTIF',
                'sisa_cuti' => 12,
                'tanggal_mulai' => Carbon::now()->toDateString(),
                'tanggal_selesai' => Carbon::now()->addMonths(1)->toDateString(),
                'user_id' => 3,
                'grup_id' => 1
            ],
            [
                'id_karyawan' => 'IN16324875',
                'ni_karyawan' => '200.100224',
                'nama' => 'INDAH NADIA HAPSARI',
                'jenis_kelamin' => 'P',
                'jenis_kontrak' => 'MAGANG',
                'status_karyawan' => 'AKTIF',
                'sisa_cuti' => 12,
                'tanggal_mulai' => Carbon::now()->toDateString(),
                'tanggal_selesai' => Carbon::now()->addMonths(1)->toDateString(),
                'user_id' => 4,
                'grup_id' => 1
            ],
            [
                'id_karyawan' => 'AM15687542',
                'ni_karyawan' => '479.030624',
                'nama' => 'AMBAR WINASTI',
                'jenis_kelamin' => 'P',
                'jenis_kontrak' => 'PKWTT',
                'status_karyawan' => 'AKTIF',
                'sisa_cuti' => 12,
                'tanggal_mulai' => Carbon::now()->toDateString(),
                'tanggal_selesai' => Carbon::now()->addMonths(1)->toDateString(),
                'user_id' => 5,
                'grup_id' => 1
            ],
            [
                'id_karyawan' => 'FA95453155',
                'ni_karyawan' => '480.030324',
                'nama' => 'FATHAN PEBRILLIESTYO RIDWAN',
                'jenis_kelamin' => 'L',
                'jenis_kontrak' => 'PKWTT',
                'status_karyawan' => 'AKTIF',
                'sisa_cuti' => 12,
                'tanggal_mulai' => Carbon::now()->toDateString(),
                'tanggal_selesai' => Carbon::now()->addMonths(1)->toDateString(),
                'user_id' => 6,
                'grup_id' => 1
            ],
        ];

        $karyawans = Karyawan::all();

        $i = 1;
        foreach ($karyawans as $kry) {
            Kontrak::create([
                'no_surat' => str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT),
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
