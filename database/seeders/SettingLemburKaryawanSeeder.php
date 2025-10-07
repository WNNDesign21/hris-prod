<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use Illuminate\Database\Seeder;
use App\Models\SettingLemburKaryawan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SettingLemburKaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $karyawan = Karyawan::find('IN16324875');
        SettingLemburKaryawan::create([
            'karyawan_id' => $karyawan->id_karyawan,
            'organisasi_id' => 2,
            'jabatan_id' => 5,
            'departemen_id' => 1,
            'gaji' => 5250000,
        ]);
    }
}
