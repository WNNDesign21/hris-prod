<?php

namespace Database\Seeders;

use App\Models\Posisi;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PosisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posisi = [
            //Direktur
            [
                'jabatan_id' => 1,
                'parent' => 0,
                'nama' => 'Administration Director',
            ],
            [
                'jabatan_id' => 1,
                'parent' => 0,
                'nama' => 'Operational Director',
            ],

            //Plant Head
            [
                'jabatan_id' => 2,
                'parent' => 0,
                'nama' => 'Plant Head TCF1',
            ],
            [
                'jabatan_id' => 2,
                'parent' => 0,
                'nama' => 'Plant Head TCF2',
            ],
            [
                'jabatan_id' => 2,
                'parent' => 0,
                'nama' => 'Plant Head TCF3',
            ],

            //Division Head
            [
                'jabatan_id' => 3,
                'parent' => 5,
                'nama' => 'Div.Head Administration TCF3',
            ],

            //Departemen Head (HRGA TCF3)
            [
                'jabatan_id' => 4,
                'parent' => 6,
                'nama' => 'Manager HRGA TCF3',
            ],

            //Supervisor (HRGA TCF3) 
            [
                'jabatan_id' => 5,
                'parent' => 7,
                'nama' => 'Supervisor HRGA TCF3',
            ],

            //Leader (HRGA TCF3)
            [
                'jabatan_id' => 6,
                'parent' => 8,
                'nama' => 'Leader HRGA TCF3',
            ],

            //Admin (HR & GA TCF3)
            [
                'jabatan_id' => 7,
                'parent' => 9,
                'nama' => 'Admin HR TCF3',
            ],
            [
                'jabatan_id' => 7,
                'parent' => 9,
                'nama' => 'Admin GA TCF3',
            ],
        ];

        foreach ($posisi as $ps) {
            Posisi::create($ps);
        }
    }
}
