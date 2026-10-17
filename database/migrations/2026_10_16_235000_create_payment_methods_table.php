<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentMethodsTable extends Migration
{
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 50)->unique()->nullable();
            $table->enum('payment_condition', ['CONTADO', 'CREDITO', 'AMBOS'])->default('CONTADO');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('requires_reference')->default(false);
            $table->string('icon', 50)->default('fas fa-money-bill-wave');
            $table->string('color', 20)->default('#2563eb');
            $table->enum('status', ['ACTIVO', 'INACTIVO'])->default('ACTIVO');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_method_id')->nullable()->after('payment_method');
            $table->string('payment_condition', 20)->default('CONTADO')->after('payment_method_id');
            $table->string('payment_reference', 100)->nullable()->after('payment_condition');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('set null');
        });

        Schema::table('sale_installments', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_method_id')->nullable()->after('payment_method');
            $table->string('payment_reference', 100)->nullable()->after('payment_method_id');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('sale_installments', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn(['payment_method_id', 'payment_reference']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn(['payment_method_id', 'payment_condition', 'payment_reference']);
        });

        Schema::dropIfExists('payment_methods');
    }
}
