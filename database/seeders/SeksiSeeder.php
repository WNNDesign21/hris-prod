<?php

namespace Database\Seeders;

use App\Models\Seksi;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SeksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seksi = [
            [
                'departemen_id' => 1,
                'nama' => 'GENERAL AFFAIR',
            ],
            [
                'departemen_id' => 1,
                'nama' => 'HR DEVELOPMENT',
            ],
        ];

        foreach ($seksi as $sk) {
            Seksi::create($sk);
        }
    }
}
