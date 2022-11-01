<?php

use Illuminate\Database\Seeder;
use Caffeinated\Shinobi\Models\Role;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        Role::create([
            'name'=>'Admin',
            'slug'=>'admin',
            'description'=>'Usuario con todos los accesos permitidos.',
            'special'=>'all-access',
        ]);
    }
}
