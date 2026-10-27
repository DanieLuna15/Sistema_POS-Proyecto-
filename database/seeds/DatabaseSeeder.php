<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Company;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(PaymentMethodsTableSeeder::class);

        // Inicializar empresa y sucursal matriz por defecto
        Company::current();
    }
}

if (!class_exists('DatabaseSeeder')) {
    class_alias(DatabaseSeeder::class, 'DatabaseSeeder');
}
