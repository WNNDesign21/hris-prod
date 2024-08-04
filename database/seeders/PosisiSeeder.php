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
                'parent_id' => 0,
                'nama' => 'BOD ADMINISTRATION',
            ],
            [
                'jabatan_id' => 1,
                'parent_id' => 0,
                'nama' => 'BOD OPERATIONAL',
            ],

            //Plant Head & Division Head
            [
                'jabatan_id' => 2,
                'parent_id' => 2,
                'nama' => 'PLANT HEAD TCF1',
                'organisasi_id' => 1,
                'divisi_id' => 4,
            ],
            [
                'jabatan_id' => 2,
                'parent_id' => 2,
                'nama' => 'PLANT HEAD TCF2',
                'organisasi_id' => 2,
                'divisi_id' => 4,
            ],
            [
                'jabatan_id' => 2,
                'parent_id' => 2,
                'nama' => 'PLANT HEAD TCF3',
                'organisasi_id' => 3,
                'divisi_id' => 4,
            ],

            [
                'jabatan_id' => 2,
                'parent_id' => 1,
                'nama' => 'DIVISION HEAD ADMINISTRATION',
                'divisi_id' => 1,
            ],

            [
                'jabatan_id' => 2,
                'parent_id' => 1,
                'nama' => 'DIVISION HEAD MARKETING & PROCUREMENT',
                'divisi_id' => 2,
            ],

            [
                'jabatan_id' => 2,
                'parent_id' => 1,
                'nama' => 'DIVISION HEAD RESEARCH & DEVELOPMENT',
                'divisi_id' => 3,
            ],

            //Departemen Head (HRGA TCF3)
            [
                'jabatan_id' => 3,
                'parent_id' => 6,
                'nama' => 'MANAGER HRGA',
                'divisi_id' => 1,
                'organisasi_id' => 3,
                'departemen_id' => 1,
            ],

            //Supervisor (HRGA TCF3) 
            [
                'jabatan_id' => 4,
                'parent_id' => 9,
                'nama' => 'SUPERVISOR GENERAL AFFAIR',
                'organisasi_id' => 3,
                'divisi_id' => 1,
                'departemen_id' => 1,
                'seksi_id' => 1,

            ],

            //Leader (HRGA TCF3)
            [
                'jabatan_id' => 5,
                'parent_id' => 10,
                'nama' => 'LEADER GENERAL AFFAIR',
                'organisasi_id' => 3,
                'divisi_id' => 1,
                'departemen_id' => 1,
                'seksi_id' => 1,
            ],

            //Admin (HR & GA TCF3)
            [
                'jabatan_id' => 6,
                'parent_id' => 11,
                'nama' => 'ADMIN GA',
                'organisasi_id' => 3,
                'divisi_id' => 1,
                'departemen_id' => 1,
                'seksi_id' => 1,
            ],
            [
                'jabatan_id' => 6,
                'parent_id' => 11,
                'nama' => 'ADMIN GA',
                'organisasi_id' => 3,
                'divisi_id' => 1,
                'departemen_id' => 1,
                'seksi_id' => 1,
            ],
        ];

        foreach ($posisi as $ps) {
            Posisi::create($ps);
        }
    }
}
