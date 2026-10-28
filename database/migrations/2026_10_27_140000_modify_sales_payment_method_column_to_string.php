<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        try {
            DB::statement("ALTER TABLE sales MODIFY payment_method VARCHAR(100) NULL DEFAULT 'EFECTIVO'");
        } catch (\Throwable $e) {}

        try {
            DB::statement("ALTER TABLE sales MODIFY sale_type VARCHAR(50) NULL DEFAULT 'REGULAR'");
        } catch (\Throwable $e) {}

        try {
            DB::statement("ALTER TABLE sale_installments MODIFY payment_method VARCHAR(100) NULL DEFAULT 'EFECTIVO'");
        } catch (\Throwable $e) {}
    }

    public function down()
    {
        //
    }
};

