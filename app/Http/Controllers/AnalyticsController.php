<?php

namespace App\Http\Controllers;

//use App\Order;
//use App\OrderDetail;
use App\Sale;
use App\Historydata;
use App\Category;
use App\Product;
use App\Purchase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Http;

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

        $getData = function($key) use ($responses) {
            try {
                if (isset($responses[$key]) && !($responses[$key] instanceof \Throwable) && $responses[$key]->successful()) {
                    return $responses[$key]->json();
                }
            } catch (\Throwable $e) {}
            return [];
        };

        $pronosticos = $getData('pronostico');
        $pronosticosfuturo = $getData('pronosticofuturo');
        $solopronosticosfuturo = $getData('solopronosticofuturo');

        $pronosticocat1 = $getData('cat1');
        $pronosticocat2 = $getData('cat2');
        $pronosticocat3 = $getData('cat3');
        $pronosticocat4 = $getData('cat4');
        $pronosticocat5 = $getData('cat5');
        $pronosticocat6 = $getData('cat6');
        $pronosticocat7 = $getData('cat7');
        $pronosticocat8 = $getData('cat8');
        $pronosticocat9 = $getData('cat9');
        $pronosticocat10 = $getData('cat10');

        $total_datahis=DB::select('SELECT c.name as namecategory,
        SUM(dh.quantity) as quantity
        from historydatas as dh
        inner join categories as c on dh.category_id=c.id
        group by c.name order by SUM(dh.quantity) desc');

        $productosvendidos=DB::select('SELECT MONTHNAME(s.sale_date) as month,
        sum(sd.quantity) as cantidad
        FROM sales as s
        inner join sale_details as sd on sd.sale_id=s.id
        WHERE YEAR(s.sale_date) = 2022
        AND s.status="CONFIRMADO"
        GROUP BY month
        ORDER BY MONTH(s.sale_date) ASC');

        $productosvendidosact=DB::select('SELECT MONTHNAME(s.sale_date) as month,
        sum(sd.quantity) as cantidad
        FROM sales as s
        inner join sale_details as sd on sd.sale_id=s.id
        WHERE YEAR(s.sale_date) = YEAR(CURDATE())
        AND s.status="CONFIRMADO"
        GROUP BY month
        ORDER BY MONTH(s.sale_date) ASC');

        $productosvendidoscat1=DB::select('SELECT MONTHNAME(s.sale_date) as month,
        sum(sd.quantity) as cantidad
        FROM sales as s
        inner join sale_details as sd on sd.sale_id=s.id
        inner join products as p on p.id = sd.product_id
        WHERE p.category_id= 1
        AND YEAR(s.sale_date) = 2022
        AND s.status="CONFIRMADO"
        GROUP BY month
        ORDER BY MONTH(s.sale_date) ASC');

        $productosvendidosactcat1=DB::select('SELECT MONTHNAME(s.sale_date) as month,
        sum(sd.quantity) as cantidad
        FROM sales as s
        inner join sale_details as sd on sd.sale_id=s.id
        inner join products as p on p.id = sd.product_id
        WHERE p.category_id= 1
        AND YEAR(s.sale_date) = YEAR(CURDATE())
        AND s.status="CONFIRMADO"
        GROUP BY month
        ORDER BY MONTH(s.sale_date) ASC');

        $productosvendidoscat2=DB::select('SELECT MONTHNAME(s.sale_date) as month,
        sum(sd.quantity) as cantidad
        FROM sales as s
        inner join sale_details as sd on sd.sale_id=s.id
        inner join products as p on p.id = sd.product_id
        WHERE p.category_id= 2
        AND YEAR(s.sale_date) = 2022
        AND s.status="CONFIRMADO"
        GROUP BY month
        ORDER BY MONTH(s.sale_date) ASC');

        $productosvendidosactcat2=DB::select('SELECT MONTHNAME(s.sale_date) as month,
        sum(sd.quantity) as cantidad
        FROM sales as s
        inner join sale_details as sd on sd.sale_id=s.id
        inner join products as p on p.id = sd.product_id
        WHERE p.category_id= 2
        AND YEAR(s.sale_date) = YEAR(CURDATE())
        AND s.status="CONFIRMADO"
        GROUP BY month
        ORDER BY MONTH(s.sale_date) ASC');

        $productosvendidoscat3=DB::select('SELECT MONTHNAME(s.sale_date) as month,
        sum(sd.quantity) as cantidad
        FROM sales as s
        inner join sale_details as sd on sd.sale_id=s.id
        inner join products as p on p.id = sd.product_id
        WHERE p.category_id= 3
        AND YEAR(s.sale_date) = 2022
        AND s.status="CONFIRMADO"
        GROUP BY month
        ORDER BY MONTH(s.sale_date) ASC');

        $productosvendidosactcat3=DB::select('SELECT MONTHNAME(s.sale_date) as month,
        sum(sd.quantity) as cantidad
        FROM sales as s
        inner join sale_details as sd on sd.sale_id=s.id
        inner join products as p on p.id = sd.product_id
        WHERE p.category_id= 3
        AND YEAR(s.sale_date) = YEAR(CURDATE())
        AND s.status="CONFIRMADO"
        GROUP BY month
        ORDER BY MONTH(s.sale_date) ASC');

        $productosvendidoscat4=DB::select('SELECT MONTHNAME(s.sale_date) as month,
        sum(sd.quantity) as cantidad
        FROM sales as s
        inner join sale_details as sd on sd.sale_id=s.id
        inner join products as p on p.id = sd.product_id
        WHERE p.category_id= 4
        AND YEAR(s.sale_date) = 2022
        AND s.status="CONFIRMADO"
        GROUP BY month
        ORDER BY MONTH(s.sale_date) ASC');

        $productosvendidosactcat4=DB::select('SELECT MONTHNAME(s.sale_date) as month,
        sum(sd.quantity) as cantidad
        FROM sales as s
        inner join sale_details as sd on sd.sale_id=s.id
        inner join products as p on p.id = sd.product_id
        WHERE p.category_id= 4
        AND YEAR(s.sale_date) = YEAR(CURDATE())
        AND s.status="CONFIRMADO"
        GROUP BY month
        ORDER BY MONTH(s.sale_date) ASC');

        $productosvendidoscat5=DB::select('SELECT MONTHNAME(s.sale_date) as month,
        sum(sd.quantity) as cantidad
        FROM sales as s
        inner join sale_details as sd on sd.sale_id=s.id
        inner join products as p on p.id = sd.product_id
        WHERE p.category_id= 5
        AND YEAR(s.sale_date) = 2022
        AND s.status="CONFIRMADO"
        GROUP BY month
        ORDER BY MONTH(s.sale_date) ASC');

        $productosvendidosactcat5=DB::select('SELECT MONTHNAME(s.sale_date) as month,
        sum(sd.quantity) as cantidad
        FROM sales as s
        inner join sale_details as sd on sd.sale_id=s.id
        inner join products as p on p.id = sd.product_id
        WHERE p.category_id= 5
        AND YEAR(s.sale_date) = YEAR(CURDATE())
        AND s.status="CONFIRMADO"
        GROUP BY month
        ORDER BY MONTH(s.sale_date) ASC');

        $productosvendidoscat6=DB::select('SELECT MONTHNAME(s.sale_date) as month,
        sum(sd.quantity) as cantidad
        FROM sales as s
        inner join sale_details as sd on sd.sale_id=s.id
        inner join products as p on p.id = sd.product_id
        WHERE p.category_id= 6
        AND YEAR(s.sale_date) = 2022
        AND s.status="CONFIRMADO"
        GROUP BY month
        ORDER BY MONTH(s.sale_date) ASC');

        $productosvendidosactcat6=DB::select('SELECT MONTHNAME(s.sale_date) as month,
        sum(sd.quantity) as cantidad
        FROM sales as s
        inner join sale_details as sd on sd.sale_id=s.id
        inner join products as p on p.id = sd.product_id
        WHERE p.category_id= 6
        AND YEAR(s.sale_date) = YEAR(CURDATE())
        AND s.status="CONFIRMADO"
        GROUP BY month
        ORDER BY MONTH(s.sale_date) ASC');

        $productosvendidoscat7=DB::select('SELECT MONTHNAME(s.sale_date) as month,
        sum(sd.quantity) as cantidad
        FROM sales as s
        inner join sale_details as sd on sd.sale_id=s.id
        inner join products as p on p.id = sd.product_id
        WHERE p.category_id= 7
        AND YEAR(s.sale_date) = 2022
        AND s.status="CONFIRMADO"
        GROUP BY month
        ORDER BY MONTH(s.sale_date) ASC');

        $productosvendidosactcat7=DB::select('SELECT MONTHNAME(s.sale_date) as month,
        sum(sd.quantity) as cantidad
        FROM sales as s
        inner join sale_details as sd on sd.sale_id=s.id
        inner join products as p on p.id = sd.product_id
        WHERE p.category_id= 7
        AND YEAR(s.sale_date) = YEAR(CURDATE())
        AND s.status="CONFIRMADO"
        GROUP BY month
        ORDER BY MONTH(s.sale_date) ASC');

        $productosvendidoscat8=DB::select('SELECT MONTHNAME(s.sale_date) as month,
        sum(sd.quantity) as cantidad
        FROM sales as s
        inner join sale_details as sd on sd.sale_id=s.id
        inner join products as p on p.id = sd.product_id
        WHERE p.category_id= 8
        AND YEAR(s.sale_date) = 2022
        AND s.status="CONFIRMADO"
        GROUP BY month
        ORDER BY MONTH(s.sale_date) ASC');

        $productosvendidosactcat8=DB::select('SELECT MONTHNAME(s.sale_date) as month,
        sum(sd.quantity) as cantidad
        FROM sales as s
        inner join sale_details as sd on sd.sale_id=s.id
        inner join products as p on p.id = sd.product_id
        WHERE p.category_id= 8
        AND YEAR(s.sale_date) = YEAR(CURDATE())
        AND s.status="CONFIRMADO"
        GROUP BY month
        ORDER BY MONTH(s.sale_date) ASC');

        $productosvendidoscat9=DB::select('SELECT MONTHNAME(s.sale_date) as month,
        sum(sd.quantity) as cantidad
        FROM sales as s
        inner join sale_details as sd on sd.sale_id=s.id
        inner join products as p on p.id = sd.product_id
        WHERE p.category_id= 9
        AND YEAR(s.sale_date) = 2022
        AND s.status="CONFIRMADO"
        GROUP BY month
        ORDER BY MONTH(s.sale_date) ASC');

        $productosvendidosactcat9=DB::select('SELECT MONTHNAME(s.sale_date) as month,
        sum(sd.quantity) as cantidad
        FROM sales as s
        inner join sale_details as sd on sd.sale_id=s.id
        inner join products as p on p.id = sd.product_id
        WHERE p.category_id= 9
        AND YEAR(s.sale_date) = YEAR(CURDATE())
        AND s.status="CONFIRMADO"
        GROUP BY month
        ORDER BY MONTH(s.sale_date) ASC');

        $productosvendidoscat10=DB::select('SELECT MONTHNAME(s.sale_date) as month,
        sum(sd.quantity) as cantidad
        FROM sales as s
        inner join sale_details as sd on sd.sale_id=s.id
        inner join products as p on p.id = sd.product_id
        WHERE p.category_id= 10
        AND YEAR(s.sale_date) = 2022
        AND s.status="CONFIRMADO"
        GROUP BY month
        ORDER BY MONTH(s.sale_date) ASC');

        $productosvendidosactcat10=DB::select('SELECT MONTHNAME(s.sale_date) as month,
        sum(sd.quantity) as cantidad
        FROM sales as s
        inner join sale_details as sd on sd.sale_id=s.id
        inner join products as p on p.id = sd.product_id
        WHERE p.category_id= 10
        AND YEAR(s.sale_date) = YEAR(CURDATE())
        AND s.status="CONFIRMADO"
        GROUP BY month
        ORDER BY MONTH(s.sale_date) ASC');

        return view('admin.analytics.index',
        compact(
            'pronosticos',
            'pronosticosfuturo',
            'solopronosticosfuturo',
            'pronosticocat1',
            'pronosticocat2',
            'pronosticocat3',
            'pronosticocat4',
            'pronosticocat5',
            'pronosticocat6',
            'pronosticocat7',
            'pronosticocat8',
            'pronosticocat9',
            'pronosticocat10',
            'historydatas',
            'categories',
            'total_datahis',
            'productosvendidos',
            'productosvendidosact',
            'productosvendidoscat1',
            'productosvendidosactcat1',
            'productosvendidoscat2',
            'productosvendidosactcat2',
            'productosvendidoscat3',
            'productosvendidosactcat3',
            'productosvendidoscat4',
            'productosvendidosactcat4',
            'productosvendidoscat5',
            'productosvendidosactcat5',
            'productosvendidoscat6',
            'productosvendidosactcat6',
            'productosvendidoscat7',
            'productosvendidosactcat7',
            'productosvendidoscat8',
            'productosvendidosactcat8',
            'productosvendidoscat9',
            'productosvendidosactcat9',
            'productosvendidoscat10',
            'productosvendidosactcat10'));
    }
}
