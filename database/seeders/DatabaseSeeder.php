<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            JeniscutiSeeder::class,
            JabatanSeeder::class,
            // SecurityRoleSeeder::class,
            // AdmindeptRoleSeeder::class,
            // OrganisasiSeeder::class,
            // PosisiSeeder::class,
            // DivisiSeeder::class,
            // DepartemenSeeder::class,
            // SeksiSeeder::class,
            // KaryawanSeeder::class,
            // SettingLemburSeeder::class,
            // SettingLemburKaryawanSeeder::class,
            // BatasLemburSeeder::class,
            // BatasApprovalLemburSeeder::class,
            // OnOffSettingBatasLemburSeeder::class,
        ]);
    }
}
