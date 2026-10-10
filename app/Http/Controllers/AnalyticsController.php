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

        //API MODELO con resiliencia ante caídas del servicio Flask
        $fetchApi = function($endpoint) {
            try {
                $res = Http::timeout(2)->get("http://127.0.0.1:5000/{$endpoint}");
                return $res->successful() ? $res->json() : [];
            } catch (\Exception $e) {
                return [];
            }
        };

        $pronosticos = $fetchApi('pronostico');
        $pronosticosfuturo = $fetchApi('pronosticofuturo');
        $solopronosticosfuturo = $fetchApi('solopronosticofuturo');

        //PARA CADA CATEGORIA
        $pronosticocat1 = $fetchApi('pronosticocat1');
        $pronosticocat2 = $fetchApi('pronosticocat2');
        $pronosticocat3 = $fetchApi('pronosticocat3');
        $pronosticocat4 = $fetchApi('pronosticocat4');
        $pronosticocat5 = $fetchApi('pronosticocat5');
        $pronosticocat6 = $fetchApi('pronosticocat6');
        $pronosticocat7 = $fetchApi('pronosticocat7');
        $pronosticocat8 = $fetchApi('pronosticocat8');
        $pronosticocat9 = $fetchApi('pronosticocat9');
        $pronosticocat10 = $fetchApi('pronosticocat10');
        //FIN API MODELO

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
