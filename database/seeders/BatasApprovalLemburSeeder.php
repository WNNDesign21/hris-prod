<?php

namespace Database\Seeders;

use App\Models\SettingLembur;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BatasApprovalLemburSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // BATAS APPROVAL
        SettingLembur::create([
            'organisasi_id' => 1,
            'setting_name' => 'batas_approval_lembur',
            'value' => '23:59',
        ]);

        SettingLembur::create([
            'organisasi_id' => 2,
            'setting_name' => 'batas_approval_lembur',
            'value' => '23:59',
        ]);
    }
}
