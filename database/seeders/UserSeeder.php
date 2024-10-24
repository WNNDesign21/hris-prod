<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //1
        Role::create([
            'name' => 'super user',
            'guard_name' => 'web'
        ]);

        //2
        Role::create([
            'name' => 'personalia',
            'guard_name' => 'web'
        ]);

        //3
        Role::create([
            'name' => 'atasan',
            'guard_name' => 'web'
        ]);

        //4
        Role::create([
            'name' => 'member',
            'guard_name' => 'web'
        ]);


        //PERSONALIA
        $personalia1_tcf2 = User::create([
            'username' => 'P1TCF2',
            'email' => "personalia1@tcf2.com",
            'password' => bcrypt('12345678'),
            'organisasi_id' => 1
        ]);

        $personalia2_tcf2 = User::create([
            'username' => 'P2TCF2',
            'email' => "personalia2@tcf2.com",
            'password' => bcrypt('12345678'),
            'organisasi_id' => 1
        ]);

        $personalia1_tcf3 = User::create([
            'username' => 'P1TCF3',
            'email' => "personalia1@tcf3.com",
            'password' => bcrypt('12345678'),
            'organisasi_id' => 2
        ]);

        $personalia2_tcf3 = User::create([
            'username' => 'P2TCF3',
            'email' => "personalia2@tcf3.com",
            'password' => bcrypt('12345678'),
            'organisasi_id' => 2
        ]);

        //SUPER USER
        $superUser = User::create([
            'username' => 'SUPERUSER',
            'email' => "superuser@tcf.com",
            'password' => bcrypt('12345678')
        ]);


        //USER
        $user1 = User::create([
            'username' => 'FL0001',
            'email' => "fl0001@tcf.com",
            'password' => bcrypt('12345678'),
            'organisasi_id' => 2
        ]);

        $user2 = User::create([
            'username' => 'IN0002',
            'email' => "in0002@email.com",
            'password' => bcrypt('12345678'),
            'organisasi_id' => 2
        ]);

        $user3 = User::create([
            'username' => 'AM0003',
            'email' => "am0003@tcf.com",
            'password' => bcrypt('12345678'),
            'organisasi_id' => 2
        ]);

        $user4 = User::create([
            'username' => 'FA1722',
            'email' => "fathan@tcf.com",
            'password' => bcrypt('12345678'),
            'organisasi_id' => 2
        ]);

        $personalia1_tcf2->assignRole('personalia');
        $personalia2_tcf2->assignRole('personalia');
        $personalia1_tcf3->assignRole('personalia');
        $personalia2_tcf3->assignRole('personalia');
        $superUser->assignRole('super user');
        $user1->assignRole('member');
        $user2->assignRole('atasan');
        $user3->assignRole('atasan');
        $user4->assignRole('atasan');
    }
}
