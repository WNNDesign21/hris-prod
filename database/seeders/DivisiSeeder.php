<?php

namespace Database\Seeders;

use App\Models\Divisi;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DivisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisi = [
            [
                'nama' => 'ADMINISTRATION',
            ],
            [
                'nama' => 'MARKETING & PROCUREMENT',
            ],
            [
                'nama' => 'RESOURCE & DEVELOPMENT',
            ],
            [
                'nama' => 'OPERATIONAL',
            ],
        ];

        foreach ($divisi as $dv) {
            Divisi::create($dv);
        }
    }
}
