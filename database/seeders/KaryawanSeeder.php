<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;

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
                'sisa_cuti' => 12,
                'tahun_masuk' => '2024',
                'user_id' => 1,
                'grup_id' => 1
            ],
            [
                'id_karyawan' => 'IN0001',
                'nama' => 'INDAH NADIA HAPSARI',
                'sisa_cuti' => 12,
                'tahun_masuk' => '2024',
                'user_id' => 2,
                'grup_id' => 1
            ],
            [
                'id_karyawan' => 'AM0001',
                'nama' => 'AMBAR WINASTI',
                'sisa_cuti' => 12,
                'tahun_masuk' => '2024',
                'user_id' => 2,
                'grup_id' => 1
            ],
        ];

        foreach ($karyawan as $kry) {
            Karyawan::create($kry);
        }
    }
}
