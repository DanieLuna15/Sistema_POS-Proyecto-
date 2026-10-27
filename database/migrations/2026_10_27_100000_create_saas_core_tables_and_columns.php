<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSaasCoreTablesAndColumns extends Migration
{
    public function up()
    {
        // 1. Tabla de Empresas / Configuración SaaS Multi-Negocio
        if (!Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150)->default('SISTEMA POS & COMERCIAL');
                $table->string('legal_name', 150)->nullable();
                $table->string('slogan', 200)->nullable()->default('Soluciones Comerciales y Distribución Multi-Rubro');
                $table->string('nit', 50)->nullable()->default('1023847029');
                $table->string('authorization_number', 80)->nullable()->default('4920018471');
                $table->string('business_type', 50)->default('GENERAL'); // GENERAL, MINIMARKET, FARMACIA, FERRETERIA, ROPA, ELECTRONICA, RESTAURANTE, SERVICIOS
                $table->string('email', 120)->nullable()->default('ventas@possystem.bo');
                $table->string('phone', 80)->nullable()->default('(+591) 4-4589210 / 71234567');
                $table->string('website', 150)->nullable();
                $table->string('address', 250)->nullable()->default('Av. Heroínas #742 esq. Ayacucho, Zona Central');
                $table->string('city', 100)->default('Cochabamba');
                $table->string('country', 100)->default('Bolivia');
                $table->string('logo', 200)->nullable();
                $table->string('currency_code', 10)->default('BOB');
                $table->string('currency_symbol', 10)->default('Bs.');
                $table->string('currency_name', 50)->default('Bolivianos');
                $table->string('tax_name', 20)->default('IVA');
                $table->decimal('tax_rate', 5, 2)->default(13.00);
                $table->string('printer_width', 10)->default('80mm');
                $table->text('receipt_footer')->nullable();
                $table->text('terms_conditions')->nullable();
                $table->integer('default_stock_alert')->default(10);

                // Switches / Módulos por Rubro de Negocio
                $table->boolean('enable_pos')->default(true);
                $table->boolean('enable_credits')->default(true);
                $table->boolean('enable_quotations')->default(true);
                $table->boolean('enable_cash_register')->default(true);
                $table->boolean('enable_kardex')->default(true);
                $table->boolean('enable_lots_expiry')->default(true);
                $table->boolean('enable_serials_warranties')->default(true);
                $table->boolean('enable_services')->default(true);
                $table->boolean('enable_wholesale')->default(true);

                $table->enum('status', ['ACTIVO', 'INACTIVO'])->default('ACTIVO');
                $table->timestamps();
            });
        }

        // 2. Tabla de Sucursales / Puntos de Venta (Multi-Sucursal)
        if (!Schema::hasTable('branches')) {
            Schema::create('branches', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id');
                $table->string('code', 30)->nullable();
                $table->string('name', 150);
                $table->string('manager_name', 120)->nullable();
                $table->string('phone', 80)->nullable();
                $table->string('email', 120)->nullable();
                $table->string('address', 250)->nullable();
                $table->string('city', 100)->nullable();
                $table->string('authorization_number', 80)->nullable();
                $table->boolean('is_main')->default(false);
                $table->enum('status', ['ACTIVO', 'INACTIVO'])->default('ACTIVO');
                $table->timestamps();

                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            });
        }

        // 3. Vincular Usuarios a Empresa y Sucursal
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('users', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('company_id');
            }
        });

        // 4. Ampliar Catálogo de Productos para Multi-Rubro
        try {
            DB::statement("ALTER TABLE products MODIFY image VARCHAR(255) NULL");
        } catch (\Throwable $e) {
            // Ignorar si ya es nullable
        }

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku', 80)->nullable()->after('code');
            }
            if (!Schema::hasColumn('products', 'item_type')) {
                $table->string('item_type', 30)->default('PRODUCTO')->after('name'); // PRODUCTO, SERVICIO, COMBO
            }
            if (!Schema::hasColumn('products', 'unit_measure')) {
                $table->string('unit_measure', 30)->default('UND')->after('item_type'); // UND, KG, LT, MT, CAJA, PAQ, SERV
            }
            if (!Schema::hasColumn('products', 'purchase_price')) {
                $table->decimal('purchase_price', 12, 2)->default(0.00)->after('image');
            }
            if (!Schema::hasColumn('products', 'wholesale_price')) {
                $table->decimal('wholesale_price', 12, 2)->nullable()->after('sell_price');
            }
            if (!Schema::hasColumn('products', 'wholesale_min_qty')) {
                $table->integer('wholesale_min_qty')->default(6)->after('wholesale_price');
            }
            if (!Schema::hasColumn('products', 'stock_min')) {
                $table->integer('stock_min')->default(5)->after('stock');
            }
            if (!Schema::hasColumn('products', 'location')) {
                $table->string('location', 100)->nullable()->after('stock_min');
            }
            if (!Schema::hasColumn('products', 'batch_number')) {
                $table->string('batch_number', 80)->nullable()->after('location');
            }
            if (!Schema::hasColumn('products', 'expiration_date')) {
                $table->date('expiration_date')->nullable()->after('batch_number');
            }
            if (!Schema::hasColumn('products', 'warranty_days')) {
                $table->integer('warranty_days')->nullable()->after('expiration_date');
            }
            if (!Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable()->after('warranty_days');
            }
            if (!Schema::hasColumn('products', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('provider_id');
            }
        });

        // 5. Tabla de Movimientos de Inventario (Kardex y Ajustes)
        if (!Schema::hasTable('inventory_movements')) {
            Schema::create('inventory_movements', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('movement_type', 40); // ENTRADA_COMPRA, SALIDA_VENTA, ANULACION_COMPRA, ANULACION_VENTA, AJUSTE_ENTRADA, AJUSTE_SALIDA, MERMA_VENCIMIENTO, INVENTARIO_INICIAL
                $table->decimal('quantity', 12, 2);
                $table->decimal('previous_stock', 12, 2)->default(0);
                $table->decimal('new_stock', 12, 2)->default(0);
                $table->decimal('unit_cost', 12, 2)->default(0);
                $table->string('reference_code', 80)->nullable();
                $table->text('notes')->nullable();
                $table->dateTime('movement_date');
                $table->timestamps();

                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            });
        }

        // 6. Tabla de Sesiones / Arqueos de Caja
        if (!Schema::hasTable('cash_registers')) {
            Schema::create('cash_registers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('closed_by_user_id')->nullable();
                $table->dateTime('opening_date');
                $table->dateTime('closing_date')->nullable();
                $table->decimal('initial_amount', 12, 2)->default(0.00);
                $table->decimal('sales_cash', 12, 2)->default(0.00);
                $table->decimal('sales_other', 12, 2)->default(0.00);
                $table->decimal('credit_collections', 12, 2)->default(0.00);
                $table->decimal('incomes_amount', 12, 2)->default(0.00);
                $table->decimal('expenses_amount', 12, 2)->default(0.00);
                $table->decimal('purchases_cash', 12, 2)->default(0.00);
                $table->decimal('expected_amount', 12, 2)->default(0.00);
                $table->decimal('counted_amount', 12, 2)->nullable();
                $table->decimal('difference', 12, 2)->nullable();
                $table->enum('status', ['ABIERTA', 'CERRADA'])->default('ABIERTA');
                $table->text('opening_notes')->nullable();
                $table->text('closing_notes')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users');
            });
        }

        // 7. Tabla de Movimientos de Caja Chica (Ingresos y Gastos Operativos)
        if (!Schema::hasTable('cash_movements')) {
            Schema::create('cash_movements', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cash_register_id')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->enum('type', ['INGRESO', 'EGRESO']);
                $table->string('category', 60)->default('GASTO_OPERATIVO');
                $table->decimal('amount', 12, 2);
                $table->string('payment_method', 60)->default('Efectivo (Bs.)');
                $table->string('reference', 100)->nullable();
                $table->text('description');
                $table->dateTime('movement_date');
                $table->timestamps();

                $table->foreign('cash_register_id')->references('id')->on('cash_registers')->onDelete('set null');
                $table->foreign('user_id')->references('id')->on('users');
            });
        }

        // 8. Tabla de Cotizaciones / Proformas
        if (!Schema::hasTable('quotations')) {
            Schema::create('quotations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('user_id');
                $table->dateTime('quotation_date');
                $table->date('valid_until')->nullable();
                $table->decimal('tax', 5, 2)->default(0.00);
                $table->decimal('subtotal', 12, 2)->default(0.00);
                $table->decimal('discount_total', 12, 2)->default(0.00);
                $table->decimal('total', 12, 2)->default(0.00);
                $table->enum('status', ['PENDIENTE', 'APROBADA', 'CONVERTIDA', 'VENCIDA', 'RECHAZADA'])->default('PENDIENTE');
                $table->unsignedBigInteger('converted_sale_id')->nullable();
                $table->text('notes')->nullable();
                $table->text('terms')->nullable();
                $table->timestamps();

                $table->foreign('client_id')->references('id')->on('clients');
                $table->foreign('user_id')->references('id')->on('users');
            });
        }

        if (!Schema::hasTable('quotation_details')) {
            Schema::create('quotation_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('quotation_id');
                $table->unsignedBigInteger('product_id');
                $table->decimal('quantity', 12, 2);
                $table->decimal('price', 12, 2);
                $table->decimal('discount', 5, 2)->default(0.00);
                $table->timestamps();

                $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products');
            });
        }

        // 9. Campos Multi-Sucursal y Caja en Ventas y Compras
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('sales', 'cash_register_id')) {
                $table->unsignedBigInteger('cash_register_id')->nullable()->after('branch_id');
            }
            if (!Schema::hasColumn('sales', 'notes')) {
                $table->text('notes')->nullable()->after('installment_frequency');
            }
        });

        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('purchases', 'cash_register_id')) {
                $table->unsignedBigInteger('cash_register_id')->nullable()->after('branch_id');
            }
            if (!Schema::hasColumn('purchases', 'notes')) {
                $table->text('notes')->nullable()->after('picture');
            }
        });
    }

    public function down()
    {
        Schema::dropIfExists('quotation_details');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('cash_movements');
        Schema::dropIfExists('cash_registers');
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('companies');
    }
}

