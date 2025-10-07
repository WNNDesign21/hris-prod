<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SecurityRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'security',
            'guard_name' => 'web'
        ]);

        //TCF 2
        $security1_tcf2 = User::create([
            'username' => 'SC1TCF2',
            'email' => "security1@tcf2.com",
            'password' => bcrypt('nevergiveup'),
            'organisasi_id' => 1
        ]);

        $security2_tcf2 = User::create([
            'username' => 'SC2TCF2',
            'email' => "security2@tcf2.com",
            'password' => bcrypt('nevergiveup'),
            'organisasi_id' => 1
        ]);

        $security3_tcf2 = User::create([
            'username' => 'SC3TCF2',
            'email' => "security3@tcf2.com",
            'password' => bcrypt('nevergiveup'),
            'organisasi_id' => 1
        ]);

        //TCF 3
        $security1_tcf3 = User::create([
            'username' => 'SC1TCF3',
            'email' => "security1@tcf3.com",
            'password' => bcrypt('nevergiveup'),
            'organisasi_id' => 2
        ]);

        $security2_tcf3 = User::create([
            'username' => 'SC2TCF3',
            'email' => "security2@tcf3.com",
            'password' => bcrypt('nevergiveup'),
            'organisasi_id' => 2
        ]);

        $security3_tcf3 = User::create([
            'username' => 'SC3TCF3',
            'email' => "security3@tcf3.com",
            'password' => bcrypt('nevergiveup'),
            'organisasi_id' => 2
        ]);

        $security1_tcf2->assignRole('security');
        $security2_tcf2->assignRole('security');
        $security3_tcf2->assignRole('security');
        $security1_tcf3->assignRole('security');
        $security2_tcf3->assignRole('security');
        $security3_tcf3->assignRole('security');
    }
}
