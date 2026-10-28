<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantTablesAndOrders extends Migration
{
    public function up()
    {
        // 1. Mesas del Restaurante por Empresa y Sucursal
        if (!Schema::hasTable('restaurant_tables')) {
            Schema::create('restaurant_tables', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->nullable()->default(1)->index();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->string('name', 60); // Ej: Mesa 1, Terraza 2, Barra 1
                $table->string('zone', 80)->default('Salón Principal'); // Salón Principal, Terraza, VIP, Barra
                $table->integer('capacity')->default(4);
                $table->string('status', 30)->default('LIBRE'); // LIBRE, OCUPADA, CUENTA_PEDIDA, RESERVADA
                $table->unsignedBigInteger('active_order_id')->nullable();
                $table->timestamps();
            });
        }

        // 2. Comandas / Pedidos de Restaurante (En Mesa, Para Llevar, Delivery)
        if (!Schema::hasTable('restaurant_orders')) {
            Schema::create('restaurant_orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->nullable()->default(1)->index();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->unsignedBigInteger('restaurant_table_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable(); // Mesero / Cajero
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('sale_id')->nullable(); // Se llena al cobrar la cuenta
                $table->string('order_number', 40)->nullable(); // CMD-0001
                $table->string('service_type', 30)->default('MESA'); // MESA, LLEVAR, DELIVERY
                $table->integer('diners_count')->default(1);
                $table->string('customer_name', 120)->nullable();
                $table->string('delivery_phone', 50)->nullable();
                $table->string('delivery_address', 250)->nullable();
                $table->string('status', 30)->default('ABIERTA'); // ABIERTA, EN_COCINA, LISTO, CUENTA_PEDIDA, COBRADA, CANCELADA
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->decimal('discount', 12, 2)->default(0);
                $table->decimal('tip_amount', 12, 2)->default(0);
                $table->decimal('total', 12, 2)->default(0);
                $table->text('notes')->nullable();
                $table->dateTime('opened_at')->nullable();
                $table->dateTime('closed_at')->nullable();
                $table->timestamps();
            });
        }

        // 3. Ítems / Platos y Bebidas de cada Comanda
        if (!Schema::hasTable('restaurant_order_items')) {
            Schema::create('restaurant_order_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('restaurant_order_id')->index();
                $table->unsignedBigInteger('product_id')->index();
                $table->decimal('quantity', 10, 2)->default(1);
                $table->decimal('price', 12, 2)->default(0);
                $table->decimal('discount', 8, 2)->default(0);
                $table->string('kitchen_status', 30)->default('EN_COCINA'); // EN_COCINA, LISTO, SERVIDO
                $table->string('notes', 250)->nullable(); // Ej: Sin cebolla, término 3/4, bien frío
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('restaurant_order_items');
        Schema::dropIfExists('restaurant_orders');
        Schema::dropIfExists('restaurant_tables');
    }
}

