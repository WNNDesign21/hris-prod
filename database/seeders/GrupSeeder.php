<?php

namespace Database\Seeders;

use App\Models\Grup;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GrupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grup = [
            [
                'nama' => 'Non Shift',
                'jam_masuk' => '07.30',
                'jam_keluar' => '16.30'
            ],
            [
                'nama' => 'Shift 1',
                'jam_masuk' => '07.30',
                'jam_keluar' => '16.30'
            ],
            [
                'nama' => 'Shift 2',
                'jam_masuk' => '07.30',
                'jam_keluar' => '16.30'
            ],
            [
                'nama' => 'Shift 3',
                'jam_masuk' => '07.30',
                'jam_keluar' => '16.30'
            ],
        ];

        foreach ($grup as $gp) {
            Grup::create($gp);
        }
    }
}
