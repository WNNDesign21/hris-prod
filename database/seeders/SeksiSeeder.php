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
                'nama' => 'People Development',
            ],
            [
                'departemen_id' => 1,
                'nama' => 'General Affairs',
            ],
        ];

        foreach ($seksi as $sk) {
            Seksi::create($sk);
        }
    }
}
