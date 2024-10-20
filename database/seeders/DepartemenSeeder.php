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

            //ADMINISTRATION
            [
                'divisi_id' => 1,
                'nama' => 'HRD & GA',
            ],
            [
                'divisi_id' => 1,
                'nama' => 'ICT',
            ],
            [
                'divisi_id' => 1,
                'nama' => 'MARKETING',
            ],
            [
                'divisi_id' => 1,
                'nama' => 'PURCHASING',
            ],
            [
                'divisi_id' => 1,
                'nama' => 'FINANCE ACCOUNTING & TAX',
            ],
            [
                'divisi_id' => 1,
                'nama' => 'QMR',
            ],

            //ENGINEERING
            [
                'divisi_id' => 2,
                'nama' => 'TOOL MAKING',
            ],
            [
                'divisi_id' => 2,
                'nama' => 'RESOURCE & DEVELOPMENT',
            ],
            [
                'divisi_id' => 2,
                'nama' => 'MANUFACTURING',
            ],
            [
                'divisi_id' => 2,
                'nama' => 'DRAWING',
            ],

            //OPERATION
            [
                'divisi_id' => 3,
                'nama' => 'PPIC',
            ],
            [
                'divisi_id' => 3,
                'nama' => 'PRODUCTION',
            ],
            [
                'divisi_id' => 3,
                'nama' => 'QUALITY CONTROL',
            ]
        ];

        foreach ($departemen as $dp) {
            Departemen::create($dp);
        }
    }
}
