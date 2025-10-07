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
                'nama' => 'ENGINEERING',
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
