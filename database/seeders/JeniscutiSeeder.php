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
                'durasi' => 3
            ],
            [
                'jenis' => 'Menikahkan Anak Kandung',
                'durasi' => 2
            ],
            [
                'jenis' => 'Khitanan Anak Kandung',
                'durasi' => 2,
            ],
            [
                'jenis' => 'Baptis Anak Kandung',
                'durasi' => 2,
            ],
            [
                'jenis' => 'Suami/Istri/Orangtua/Mertua/Anak/Menantu/Kakek/Nenek Meninggal',
                'durasi' => 2,
            ],
            [
                'jenis' => 'Saudara Kandung/Serumah Meninggal',
                'durasi' => 1,
            ],
        ];

        foreach ($jenis_cuti as $jc) {
            JenisCuti::create($jc);
        }
    }
}
