<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\User;
use Caffeinated\Shinobi\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // 1. Obtener o crear el rol de Administrador con acceso total
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrador',
                'description' => 'Usuario con todos los accesos permitidos.',
                'special' => 'all-access',
            ]
        );

        if ($adminRole->special !== 'all-access') {
            $adminRole->update(['special' => 'all-access']);
        }

        // 2. Crear o actualizar el usuario admin estándar
        $admin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('12345678'),
                'status' => 'HABILITADO',
                'email_verified_at' => now(),
            ]
        );

        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        // 3. También restablecer la clave del usuario existente si ya existía
        $existingAdmin = User::where('email', 'danilunajose123@gmail.com')->first();
        if ($existingAdmin) {
            $existingAdmin->update([
                'password' => Hash::make('12345678'),
                'status' => 'HABILITADO',
            ]);
            $existingAdmin->roles()->syncWithoutDetaching([$adminRole->id]);
        }
    }
}

if (!class_exists('UsersTableSeeder')) {
    class_alias(UsersTableSeeder::class, 'UsersTableSeeder');
}
