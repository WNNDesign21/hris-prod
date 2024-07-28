<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jabatan = [
            [
                'nama' => 'BOD',
            ],
            [
                'nama' => 'PLANT HEAD',
            ],
            [
                'nama' => 'DIVISION HEAD',
            ],
            [
                'nama' => 'DEPARTEMEN HEAD',
            ],
            [
                'nama' => 'SECTION HEAD',
            ],
            [
                'nama' => 'LEADER',
            ],
            [
                'nama' => 'STAFF',
            ],
        ];

        foreach ($jabatan as $jb) {
            Jabatan::create($jb);
        }
    }
}
