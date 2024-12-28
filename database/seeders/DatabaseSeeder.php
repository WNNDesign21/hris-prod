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
        // User::factory(10)->create();

        //TRIAL 3 ACCOUNT USER
        // User::factory()->create([
        //     'username' => 'FL0001',
        //     'email' => 'fl0001@email.com',
        //     'password' =>  Hash::make('password')
        //   ])->create([
        //     'username' => 'IN0002',
        //     'email' => 'in0002@email.com',
        //     'password' =>  Hash::make('password')
        //   ])->create([
        //     'username' => 'AM0003',
        //     'email' => 'am0003@email.com',
        //     'password' =>  Hash::make('password')
        //   ]);


        $this->call([
            UserSeeder::class,
            StoSeeder::class
            // EventSeeder::class,
            // JeniscutiSeeder::class,
            // OrganisasiSeeder::class,
            // JabatanSeeder::class,
            // PosisiSeeder::class,
            // DivisiSeeder::class,
            // DepartemenSeeder::class,
            // SeksiSeeder::class,
            // GrupSeeder::class,
            // KaryawanSeeder::class,
            // SettingLemburKaryawanSeeder::class,
        ]);
    }
}
