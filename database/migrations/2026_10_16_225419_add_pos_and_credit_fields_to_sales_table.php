<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->enum('sale_type', ['REGULAR', 'POS_RAPIDO', 'CREDITO'])->default('REGULAR')->after('status');
            $table->enum('payment_method', ['EFECTIVO', 'QR', 'TARJETA', 'CREDITO', 'MIXTO'])->default('EFECTIVO')->after('sale_type');
            $table->decimal('cash_received', 12, 2)->nullable()->after('payment_method');
            $table->decimal('cash_change', 12, 2)->nullable()->after('cash_received');

            // Campos específicos para ventas a crédito
            $table->decimal('down_payment', 12, 2)->default(0)->after('cash_change');
            $table->decimal('balance_pending', 12, 2)->default(0)->after('down_payment');
            $table->enum('credit_status', ['PENDIENTE', 'PAGADO', 'VENCIDO'])->nullable()->after('balance_pending');
            $table->integer('installments_count')->nullable()->after('credit_status');
            $table->enum('installment_frequency', ['SEMANAL', 'QUINCENAL', 'MENSUAL'])->nullable()->after('installments_count');
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'sale_type',
                'payment_method',
                'cash_received',
                'cash_change',
                'down_payment',
                'balance_pending',
                'credit_status',
                'installments_count',
                'installment_frequency',
            ]);
        });
    }
};
