<?php

namespace Database\Seeders;

use App\Models\SettingLembur;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SettingLemburSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings_tcf3 = [
            [
                'organisasi_id' => 2,
                'setting_name' => 'uang_makan',
                'value' => 15000,
            ],
            [
                'organisasi_id' => 2,
                'setting_name' => 'pembagi_upah_lembur_harian',
                'value' => 173,
            ],

            //JAM ISTIRAHAT 1
            [
                'organisasi_id' => 2,
                'setting_name' => 'jam_istirahat_mulai_1',
                'value' => '12:00',
            ],
            [
                'organisasi_id' => 2,
                'setting_name' => 'jam_istirahat_selesai_1',
                'value' => '12:45',
            ],

            //JAM ISTIRAHAT 2
            [
                'organisasi_id' => 2,
                'setting_name' => 'jam_istirahat_mulai_2',
                'value' => '18:00',
            ],
            [
                'organisasi_id' => 2,
                'setting_name' => 'jam_istirahat_selesai_2',
                'value' => '18:45',
            ],

            //JAM ISTIRAHAT 3
            [
                'organisasi_id' => 2,
                'setting_name' => 'jam_istirahat_mulai_3',
                'value' => '02:30',
            ],
            [
                'organisasi_id' => 2,
                'setting_name' => 'jam_istirahat_selesai_3',
                'value' => '03:15',
            ],

            //JAM ISTIRAHAT 4 (JUMAT)
            [
                'organisasi_id' => 2,
                'setting_name' => 'jam_istirahat_mulai_jumat',
                'value' => '11:30',
            ],
            [
                'organisasi_id' => 2,
                'setting_name' => 'jam_istirahat_selesai_jumat',
                'value' => '13:00',
            ],

            //DURASI ISTIRAHAT NORMAL
            [
                'organisasi_id' => 2,
                'setting_name' => 'durasi_istirahat_1',
                'value' => 45,
            ],

            [
                'organisasi_id' => 2,
                'setting_name' => 'durasi_istirahat_2',
                'value' => 45,
            ],

            [
                'organisasi_id' => 2,
                'setting_name' => 'durasi_istirahat_3',
                'value' => 45,
            ],

            [
                'organisasi_id' => 2,
                'setting_name' => 'durasi_istirahat_jumat',
                'value' => 90,
            ],

            //INSENTIF SECTION HEAD
            [
                'organisasi_id' => 2,
                'setting_name' => 'insentif_section_head_1',
                'value' => 32500,
            ],
            [
                'organisasi_id' => 2,
                'setting_name' => 'insentif_section_head_2',
                'value' => 67500,
            ],
            [
                'organisasi_id' => 2,
                'setting_name' => 'insentif_section_head_3',
                'value' => 107500,
            ],
            [
                'organisasi_id' => 2,
                'setting_name' => 'insentif_section_head_4',
                'value' => 250000,
            ],

            //INSENTIF DEPTHEAD
            [
                'organisasi_id' => 2,
                'setting_name' => 'insentif_department_head_4',
                'value' => 400000,
            ],
        ];

        $settings_tcf2 = [
            [
                'organisasi_id' => 1,
                'setting_name' => 'uang_makan',
                'value' => 15000,
            ],
            [
                'organisasi_id' => 1,
                'setting_name' => 'pembagi_upah_lembur_harian',
                'value' => 173,
            ],

            //JAM ISTIRAHAT 1
            [
                'organisasi_id' => 1,
                'setting_name' => 'jam_istirahat_mulai_1',
                'value' => '12:00',
            ],
            [
                'organisasi_id' => 1,
                'setting_name' => 'jam_istirahat_selesai_1',
                'value' => '12:45',
            ],

            //JAM ISTIRAHAT 2
            [
                'organisasi_id' => 1,
                'setting_name' => 'jam_istirahat_mulai_2',
                'value' => '18:00',
            ],
            [
                'organisasi_id' => 1,
                'setting_name' => 'jam_istirahat_selesai_2',
                'value' => '18:45',
            ],

            //JAM ISTIRAHAT 3
            [
                'organisasi_id' => 1,
                'setting_name' => 'jam_istirahat_mulai_3',
                'value' => '02:30',
            ],
            [
                'organisasi_id' => 1,
                'setting_name' => 'jam_istirahat_selesai_3',
                'value' => '03:15',
            ],

            //JAM ISTIRAHAT 4 (JUMAT)
            [
                'organisasi_id' => 1,
                'setting_name' => 'jam_istirahat_mulai_jumat',
                'value' => '11:30',
            ],
            [
                'organisasi_id' => 1,
                'setting_name' => 'jam_istirahat_selesai_jumat',
                'value' => '13:00',
            ],

            //DURASI ISTIRAHAT NORMAL
            [
                'organisasi_id' => 1,
                'setting_name' => 'durasi_istirahat_1',
                'value' => 45,
            ],

            [
                'organisasi_id' => 1,
                'setting_name' => 'durasi_istirahat_2',
                'value' => 45,
            ],

            [
                'organisasi_id' => 1,
                'setting_name' => 'durasi_istirahat_3',
                'value' => 45,
            ],

            [
                'organisasi_id' => 1,
                'setting_name' => 'durasi_istirahat_jumat',
                'value' => 90,
            ],

            //INSENTIF SECTION HEAD
            [
                'organisasi_id' => 1,
                'setting_name' => 'insentif_section_head_1',
                'value' => 32500,
            ],
            [
                'organisasi_id' => 1,
                'setting_name' => 'insentif_section_head_2',
                'value' => 67500,
            ],
            [
                'organisasi_id' => 1,
                'setting_name' => 'insentif_section_head_3',
                'value' => 107500,
            ],
            [
                'organisasi_id' => 1,
                'setting_name' => 'insentif_section_head_4',
                'value' => 250000,
            ],

            //INSENTIF DEPTHEAD
            [
                'organisasi_id' => 1,
                'setting_name' => 'insentif_department_head_4',
                'value' => 400000,
            ],
        ];

        foreach ($settings_tcf3 as $settings){
            SettingLembur::create($settings);
        }

        foreach ($settings_tcf2 as $settings){
            SettingLembur::create($settings);
        }
    }
}
