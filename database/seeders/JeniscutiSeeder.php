<?php

namespace Database\Seeders;

use App\Models\JenisCuti;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JeniscutiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenis_cuti = [
            [
                'jenis' => 'Menikah',
                'durasi' => 3,
                'isUrgent' => 'N'
            ],
            [
                'jenis' => 'Menikahkan Anak Kandung',
                'durasi' => 2,
                'isUrgent' => 'N'
            ],
            [
                'jenis' => 'Khitanan Anak Kandung',
                'durasi' => 2,
                'isUrgent' => 'N'
            ],
            [
                'jenis' => 'Baptis Anak Kandung',
                'durasi' => 2,
                'isUrgent' => 'N'
            ],
            [
                'jenis' => 'Suami/Istri/Orangtua/Mertua/Anak/Menantu/Kakek/Nenek Meninggal',
                'durasi' => 2,
                'isUrgent' => 'Y'
            ],
            [
                'jenis' => 'Saudara Kandung/Serumah Meninggal',
                'durasi' => 1,
                'isUrgent' => 'Y'
            ],
        ];

        foreach ($jenis_cuti as $jc) {
            JenisCuti::create($jc);
        }
    }
}
