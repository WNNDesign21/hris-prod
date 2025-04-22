<?php

namespace Database\Seeders;

use App\Models\SettingLembur;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OnOffSettingBatasLemburSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // BATAS PENGAJUAN
         SettingLembur::create([
            'organisasi_id' => 1,
            'setting_name' => 'onoff_batas_pengajuan_lembur',
            'value' => 'Y',
        ]);

        SettingLembur::create([
            'organisasi_id' => 2,
            'setting_name' => 'onoff_batas_pengajuan_lembur',
            'value' => 'Y',
        ]);

        // BATAS APPROVAL
        SettingLembur::create([
            'organisasi_id' => 1,
            'setting_name' => 'onoff_batas_approval_lembur',
            'value' => 'Y',
        ]);

        SettingLembur::create([
            'organisasi_id' => 2,
            'setting_name' => 'onoff_batas_approval_lembur',
            'value' => 'Y',
        ]);
    }
}
