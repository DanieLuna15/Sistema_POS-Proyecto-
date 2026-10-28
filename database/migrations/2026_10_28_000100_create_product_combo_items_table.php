<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('product_combo_items')) {
            Schema::create('product_combo_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('combo_product_id')->index();
                $table->unsignedBigInteger('component_product_id')->index();
                $table->decimal('quantity', 10, 2)->default(1);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('product_combo_items');
    }
};

