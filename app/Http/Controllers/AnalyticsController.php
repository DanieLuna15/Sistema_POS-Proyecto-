<?php

namespace App\Http\Controllers;

use App\Sale;
use App\Historydata;
use App\Category;
use App\Product;
use App\Purchase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:analytics.index')->only(['index']);
    }

    public function index()
    {
        $categories = Category::get();
        $historydatas = Historydata::get();

        // Peticiones concurrentes en paralelo a la API de Flask con timeout reducido
        try {
            $responses = Http::pool(fn ($pool) => [
                $pool->as('pronostico')->timeout(1)->get("http://127.0.0.1:5000/pronostico"),
                $pool->as('pronosticofuturo')->timeout(1)->get("http://127.0.0.1:5000/pronosticofuturo"),
                $pool->as('solopronosticofuturo')->timeout(1)->get("http://127.0.0.1:5000/solopronosticofuturo"),
                $pool->as('cat1')->timeout(1)->get("http://127.0.0.1:5000/pronosticocat1"),
                $pool->as('cat2')->timeout(1)->get("http://127.0.0.1:5000/pronosticocat2"),
                $pool->as('cat3')->timeout(1)->get("http://127.0.0.1:5000/pronosticocat3"),
                $pool->as('cat4')->timeout(1)->get("http://127.0.0.1:5000/pronosticocat4"),
                $pool->as('cat5')->timeout(1)->get("http://127.0.0.1:5000/pronosticocat5"),
                $pool->as('cat6')->timeout(1)->get("http://127.0.0.1:5000/pronosticocat6"),
                $pool->as('cat7')->timeout(1)->get("http://127.0.0.1:5000/pronosticocat7"),
                $pool->as('cat8')->timeout(1)->get("http://127.0.0.1:5000/pronosticocat8"),
                $pool->as('cat9')->timeout(1)->get("http://127.0.0.1:5000/pronosticocat9"),
                $pool->as('cat10')->timeout(1)->get("http://127.0.0.1:5000/pronosticocat10"),
            ]);
        } catch (\Throwable $e) {
            $responses = [];
        }

        $getData = function ($key) use ($responses) {
            try {
                if (isset($responses[$key]) && !($responses[$key] instanceof \Throwable) && $responses[$key]->successful()) {
                    return $responses[$key]->json();
                }
            } catch (\Throwable $e) {}
            return [];
        };

        $data = [
            'pronosticos' => $getData('pronostico'),
            'pronosticosfuturo' => $getData('pronosticofuturo'),
            'solopronosticosfuturo' => $getData('solopronosticofuturo'),
            'historydatas' => $historydatas,
            'categories' => $categories,
        ];

        for ($i = 1; $i <= 10; $i++) {
            $data["pronosticocat{$i}"] = $getData("cat{$i}");
        }

        $data['total_datahis'] = DB::select('SELECT c.name as namecategory,
            SUM(dh.quantity) as quantity
            FROM historydatas as dh
            INNER JOIN categories as c ON dh.category_id = c.id
            GROUP BY c.name ORDER BY SUM(dh.quantity) DESC');

        // Determinar el año histórico más reciente con datos (o 2022 por defecto)
        $historicalYear = DB::table('sales')
            ->where('status', 'CONFIRMADO')
            ->whereRaw('YEAR(sale_date) < YEAR(CURDATE())')
            ->selectRaw('MAX(YEAR(sale_date)) as yr')
            ->value('yr') ?? 2022;

        $data['productosvendidos'] = DB::select('SELECT MONTHNAME(s.sale_date) as month,
            SUM(sd.quantity) as cantidad
            FROM sales as s
            INNER JOIN sale_details as sd ON sd.sale_id = s.id
            WHERE YEAR(s.sale_date) = ?
            AND s.status = "CONFIRMADO"
            GROUP BY month, MONTH(s.sale_date)
            ORDER BY MONTH(s.sale_date) ASC', [$historicalYear]);

        $data['productosvendidosact'] = DB::select('SELECT MONTHNAME(s.sale_date) as month,
            SUM(sd.quantity) as cantidad
            FROM sales as s
            INNER JOIN sale_details as sd ON sd.sale_id = s.id
            WHERE YEAR(s.sale_date) = YEAR(CURDATE())
            AND s.status = "CONFIRMADO"
            GROUP BY month, MONTH(s.sale_date)
            ORDER BY MONTH(s.sale_date) ASC');

        // Consultas dinámicas por categoría (1..10) evitando código duplicado
        for ($catId = 1; $catId <= 10; $catId++) {
            $data["productosvendidoscat{$catId}"] = DB::select('SELECT MONTHNAME(s.sale_date) as month,
                SUM(sd.quantity) as cantidad
                FROM sales as s
                INNER JOIN sale_details as sd ON sd.sale_id = s.id
                INNER JOIN products as p ON p.id = sd.product_id
                WHERE p.category_id = ?
                AND YEAR(s.sale_date) = ?
                AND s.status = "CONFIRMADO"
                GROUP BY month, MONTH(s.sale_date)
                ORDER BY MONTH(s.sale_date) ASC', [$catId, $historicalYear]);

            $data["productosvendidosactcat{$catId}"] = DB::select('SELECT MONTHNAME(s.sale_date) as month,
                SUM(sd.quantity) as cantidad
                FROM sales as s
                INNER JOIN sale_details as sd ON sd.sale_id = s.id
                INNER JOIN products as p ON p.id = sd.product_id
                WHERE p.category_id = ?
                AND YEAR(s.sale_date) = YEAR(CURDATE())
                AND s.status = "CONFIRMADO"
                GROUP BY month, MONTH(s.sale_date)
                ORDER BY MONTH(s.sale_date) ASC', [$catId]);
        }

        return view('admin.analytics.index', $data);
    }
}
