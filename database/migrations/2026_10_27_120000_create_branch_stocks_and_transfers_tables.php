<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateBranchStocksAndTransfersTables extends Migration
{
    public function up()
    {
        // 1. Tabla de Stock Independiente por Sucursal (branch_products)
        if (!Schema::hasTable('branch_products')) {
            Schema::create('branch_products', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->nullable()->default(1)->index();
                $table->unsignedBigInteger('branch_id')->index();
                $table->unsignedBigInteger('product_id')->index();
                $table->integer('stock')->default(0);
                $table->integer('stock_min')->default(5);
                $table->string('location', 100)->nullable();
                $table->timestamps();

                $table->unique(['branch_id', 'product_id'], 'branch_product_unique');
            });
        }

        // 2. Tabla de Traspasos de Mercadería entre Sucursales (stock_transfers)
        if (!Schema::hasTable('stock_transfers')) {
            Schema::create('stock_transfers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->nullable()->default(1)->index();
                $table->unsignedBigInteger('from_branch_id')->index();
                $table->unsignedBigInteger('to_branch_id')->index();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->dateTime('transfer_date');
                $table->string('reference_code', 40)->nullable();
                $table->string('status', 30)->default('COMPLETADO');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 3. Detalle de Ítems Traspasados (stock_transfer_details)
        if (!Schema::hasTable('stock_transfer_details')) {
            Schema::create('stock_transfer_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('stock_transfer_id')->index();
                $table->unsignedBigInteger('product_id')->index();
                $table->decimal('quantity', 12, 2)->default(1);
                $table->decimal('unit_cost', 12, 2)->default(0);
                $table->timestamps();
            });
        }

        // 4. Inicializar el stock actual de los productos existentes en su Sucursal Principal
        try {
            $products = DB::table('products')->get();
            foreach ($products as $prod) {
                $companyId = $prod->company_id ?? 1;
                $branchId = $prod->branch_id;

                if (!$branchId) {
                    $branchId = DB::table('branches')
                        ->where('company_id', $companyId)
                        ->orderBy('is_main', 'DESC')
                        ->orderBy('id', 'ASC')
                        ->value('id');
                }

                if ($branchId) {
                    DB::table('branch_products')->updateOrInsert(
                        ['branch_id' => $branchId, 'product_id' => $prod->id],
                        [
                            'company_id' => $companyId,
                            'stock' => intval($prod->stock ?? 0),
                            'stock_min' => intval($prod->stock_min ?? 5),
                            'location' => $prod->location,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        } catch (\Throwable $e) {}
    }

    public function down()
    {
        Schema::dropIfExists('stock_transfer_details');
        Schema::dropIfExists('stock_transfers');
        Schema::dropIfExists('branch_products');
    }
}

