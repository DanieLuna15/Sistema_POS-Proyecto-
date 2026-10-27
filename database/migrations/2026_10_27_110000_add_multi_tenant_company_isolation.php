<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddMultiTenantCompanyIsolation extends Migration
{
    public function up()
    {
        // 1. Campos de Suscripción SaaS en companies
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'plan_name')) {
                $table->string('plan_name', 50)->default('EMPRESARIAL')->after('business_type');
            }
            if (!Schema::hasColumn('companies', 'subscription_ends_at')) {
                $table->date('subscription_ends_at')->nullable()->after('plan_name');
            }
            if (!Schema::hasColumn('companies', 'max_users')) {
                $table->integer('max_users')->default(15)->after('subscription_ends_at');
            }
            if (!Schema::hasColumn('companies', 'max_branches')) {
                $table->integer('max_branches')->default(5)->after('max_users');
            }
            if (!Schema::hasColumn('companies', 'owner_user_id')) {
                $table->unsignedBigInteger('owner_user_id')->nullable()->after('id');
            }
        });

        // 2. Campo is_superadmin en users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_superadmin')) {
                $table->boolean('is_superadmin')->default(false)->after('status');
            }
        });

        // 3. Agregar company_id a todas las tablas operativas del negocio
        $tables = [
            'categories',
            'brands',
            'providers',
            'products',
            'clients',
            'payment_methods',
            'purchases',
            'sales',
            'quotations',
            'inventory_movements',
            'cash_movements',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'company_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unsignedBigInteger('company_id')->nullable()->default(1)->after('id');
                    $table->index('company_id');
                });
            }
        }

        // 4. Relajar índices unique globales para permitir que distintas empresas usen los mismos nombres/códigos
        $dropUniqueIndexes = [
            'categories' => ['categories_name_unique'],
            'brands' => ['brands_name_unique'],
            'products' => ['products_code_unique', 'products_name_unique'],
            'providers' => ['providers_name_unique', 'providers_email_unique', 'providers_nit_unique', 'providers_phone_unique'],
            'clients' => ['clients_ci_unique', 'clients_nit_unique', 'clients_phone_unique', 'clients_email_unique'],
            'payment_methods' => ['payment_methods_code_unique'],
        ];

        foreach ($dropUniqueIndexes as $table => $indexes) {
            foreach ($indexes as $indexName) {
                try {
                    DB::statement("ALTER TABLE {$table} DROP INDEX {$indexName}");
                } catch (\Throwable $e) {
                    // Ya fue eliminado o no existe
                }
            }
        }

        // 5. Asignar datos existentes a la Empresa #1 y marcar los administradores principales como SuperAdmin SaaS
        try {
            $firstCompanyId = DB::table('companies')->value('id') ?? 1;
            DB::table('users')->whereNull('company_id')->update(['company_id' => $firstCompanyId]);
            DB::table('users')
                ->whereIn('email', ['admin@admin.com', 'danilunajose123@gmail.com'])
                ->update(['is_superadmin' => true, 'company_id' => $firstCompanyId]);

            foreach ($tables as $t) {
                if (Schema::hasTable($t)) {
                    DB::table($t)->whereNull('company_id')->update(['company_id' => $firstCompanyId]);
                }
            }
            if (Schema::hasTable('cash_registers')) {
                DB::table('cash_registers')->whereNull('company_id')->update(['company_id' => $firstCompanyId]);
            }
        } catch (\Throwable $e) {}
    }

    public function down()
    {
        //
    }
}

