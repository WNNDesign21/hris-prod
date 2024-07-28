<?php

namespace Database\Seeders;

use App\Models\Departemen;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //Trial Using HRGA Departemen
        $departemen = [
            [
                'divisi_id' => 1,
                'nama' => 'HRGA',
            ],
            [
                'divisi_id' => 1,
                'nama' => 'ICT',
            ],
        ];

        foreach ($departemen as $dp) {
            Departemen::create($dp);
        }
    }
}
