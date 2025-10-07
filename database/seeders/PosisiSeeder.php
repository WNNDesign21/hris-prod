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
                'nama' => 'ADMINISTRATION DIRECTOR',
            ],
            [
                'jabatan_id' => 1,
                'parent_id' => 0,
                'nama' => 'OPERATIONAL DIRECTOR',
            ],

            //Plant Head & Division Head
            [
                'jabatan_id' => 2,
                'parent_id' => 2,
                'nama' => 'PLANT HEAD TCF2',
                'organisasi_id' => 1,
                'divisi_id' => 3,
            ],
            [
                'jabatan_id' => 2,
                'parent_id' => 2,
                'nama' => 'PLANT HEAD TCF3',
                'organisasi_id' => 2,
                'divisi_id' => 3,
            ],

            [
                'jabatan_id' => 2,
                'parent_id' => 1,
                'nama' => 'DIVISION HEAD ADMINISTRATION',
                'divisi_id' => 1,
            ],

            [
                'jabatan_id' => 2,
                'parent_id' => 2,
                'nama' => 'DIVISION ENGINEERING',
                'divisi_id' => 2,
            ],

            //Departemen Head (HRGA TCF3)
            [
                'jabatan_id' => 3,
                'parent_id' => 5,
                'nama' => 'DEPARTMEN HEAD HRD & GA',
                'divisi_id' => 1,
                'organisasi_id' => 2,
                'departemen_id' => 1,
            ],

            //Supervisor (HRGA TCF3) 
            [
                'jabatan_id' => 4,
                'parent_id' => 7,
                'nama' => 'SUPERVISOR GENERAL AFFAIR',
                'organisasi_id' => 2,
                'divisi_id' => 1,
                'departemen_id' => 1,
                'seksi_id' => 1,

            ],

            //Leader (HRGA TCF3)
            [
                'jabatan_id' => 5,
                'parent_id' => 8,
                'nama' => 'LEADER GENERAL AFFAIR',
                'organisasi_id' => 2,
                'divisi_id' => 1,
                'departemen_id' => 1,
                'seksi_id' => 1,
            ],

            //Admin (HR & GA TCF3)
            [
                'jabatan_id' => 6,
                'parent_id' => 9,
                'nama' => 'STAFF ADMINISTRASI GA',
                'organisasi_id' => 2,
                'divisi_id' => 1,
                'departemen_id' => 1,
                'seksi_id' => 1,
            ],
            [
                'jabatan_id' => 6,
                'parent_id' => 9,
                'nama' => 'STAFF ADMINISTASI HRD',
                'organisasi_id' => 2,
                'divisi_id' => 1,
                'departemen_id' => 1,
                'seksi_id' => 2,
            ],
        ];

        foreach ($posisi as $ps) {
            Posisi::create($ps);
        }
    }
}
