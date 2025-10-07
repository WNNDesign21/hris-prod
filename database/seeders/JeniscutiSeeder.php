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
                'isUrgent' => 'N',
                'isWorkday' => 'Y'
            ],
            [
                'jenis' => 'Menikahkan Anak Kandung',
                'durasi' => 2,
                'isUrgent' => 'N',
                'isWorkday' => 'Y'
            ],
            [
                'jenis' => 'Khitanan Anak Kandung',
                'durasi' => 2,
                'isUrgent' => 'N',
                'isWorkday' => 'Y'
            ],
            [
                'jenis' => 'Baptis Anak Kandung',
                'durasi' => 2,
                'isUrgent' => 'N',
                'isWorkday' => 'Y'
            ],
            [
                'jenis' => 'Suami/Istri/Orangtua/Mertua/Anak/Menantu/Kakek/Nenek Meninggal',
                'durasi' => 2,
                'isUrgent' => 'Y',
                'isWorkday' => 'Y'
            ],
            [
                'jenis' => 'Saudara Kandung/Serumah Meninggal',
                'durasi' => 1,
                'isUrgent' => 'Y',
                'isWorkday' => 'Y'
            ],
            [
                'jenis' => 'Keguguran',
                'durasi' => 45,
                'isUrgent' => 'Y',
                'isWorkday' => 'N'
            ],
            [
                'jenis' => 'Melahirkan',
                'durasi' => 90,
                'isUrgent' => 'Y',
                'isWorkday' => 'N'
            ],
            [
                'jenis' => 'Ibadah Haji',
                'durasi' => 40,
                'isUrgent' => 'N',
                'isWorkday' => 'N'
            ],
        ];

        foreach ($jenis_cuti as $jc) {
            JenisCuti::create($jc);
        }
    }
}
