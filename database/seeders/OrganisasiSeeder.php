<?php

namespace Database\Seeders;

use App\Models\Organisasi;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrganisasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organisasi = [
            [
                'nama' => 'SADANG',
                'alamat' => 'Jl. Sadang - Subang KM 10 Kp. Karajan RT 004/001, Cibatu, Purwakarta Regency, West Java 41181',
            ],
            [
                'nama' => 'KIM',
                'alamat' => 'Jl. Mitra Raya II No.6, Parungmulya, Kec. Ciampel, Karawang, Jawa Barat 41363',
            ],
        ];

        foreach ($organisasi as $org) {
            Organisasi::create($org);
        }
    }
}
