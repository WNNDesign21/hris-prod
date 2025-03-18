<?php

namespace Database\Seeders;

use App\Models\SettingLembur;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BatasLemburSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SettingLembur::create([
            'organisasi_id' => 1,
            'setting_name' => 'batas_pengajuan_lembur',
            'value' => '17:00',
        ]);

        SettingLembur::create([
            'organisasi_id' => 2,
            'setting_name' => 'batas_pengajuan_lembur',
            'value' => '17:00',
        ]);
    }
}
