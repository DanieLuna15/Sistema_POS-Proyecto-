<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('restaurant_tables', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurant_tables', 'shape')) {
                $table->string('shape', 30)->default('CUADRADA')->after('capacity'); // CUADRADA, REDONDA, RECTANGULAR, BARRA
            }
            if (!Schema::hasColumn('restaurant_tables', 'pos_x')) {
                $table->integer('pos_x')->default(40)->after('shape');
            }
            if (!Schema::hasColumn('restaurant_tables', 'pos_y')) {
                $table->integer('pos_y')->default(40)->after('pos_x');
            }
        });

        // Asignar coordenadas iniciales ordenadas a las mesas existentes que tengan pos_x = 40 y pos_y = 40
        $tables = DB::table('restaurant_tables')->orderBy('id')->get();
        $zoneCounters = [];
        foreach ($tables as $t) {
            $z = $t->zone ?: 'Salón Principal';
            if (!isset($zoneCounters[$z])) {
                $zoneCounters[$z] = 0;
            }
            $idx = $zoneCounters[$z]++;
            $col = $idx % 4;
            $row = intdiv($idx, 4);
            $x = 40 + ($col * 190);
            $y = 40 + ($row * 160);
            $shape = $t->capacity >= 6 ? 'RECTANGULAR' : ($t->capacity == 2 ? 'REDONDA' : 'CUADRADA');
            if (stripos($z, 'barra') !== false) {
                $shape = 'BARRA';
            }
            DB::table('restaurant_tables')->where('id', $t->id)->update([
                'pos_x' => $x,
                'pos_y' => $y,
                'shape' => $shape,
            ]);
        }
    }

    public function down()
    {
        Schema::table('restaurant_tables', function (Blueprint $table) {
            $table->dropColumn(['shape', 'pos_x', 'pos_y']);
        });
    }
};

