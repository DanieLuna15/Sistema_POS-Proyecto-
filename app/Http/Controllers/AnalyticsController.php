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

        //API MODELO
        $respuesta = Http::get('http://127.0.0.1:5000/pronostico');
        $pronosticos=$respuesta->json();

        $respuestafuturo = Http::get('http://127.0.0.1:5000/pronosticofuturo');
        $pronosticosfuturo=$respuestafuturo->json();

        $solorespuestafuturo = Http::get('http://127.0.0.1:5000/solopronosticofuturo');
        $solopronosticosfuturo=$solorespuestafuturo->json();

        //PARA CADA CATEGORIA
        $respuestacat1 = Http::get('http://127.0.0.1:5000/pronosticocat1');
        $pronosticocat1=$respuestacat1->json();

        $respuestacat2 = Http::get('http://127.0.0.1:5000/pronosticocat2');
        $pronosticocat2=$respuestacat2->json();

        $respuestacat3 = Http::get('http://127.0.0.1:5000/pronosticocat3');
        $pronosticocat3=$respuestacat3->json();

        $respuestacat4 = Http::get('http://127.0.0.1:5000/pronosticocat4');
        $pronosticocat4=$respuestacat4->json();

        $respuestacat5 = Http::get('http://127.0.0.1:5000/pronosticocat5');
        $pronosticocat5=$respuestacat5->json();

        $respuestacat6 = Http::get('http://127.0.0.1:5000/pronosticocat6');
        $pronosticocat6=$respuestacat6->json();

        $respuestacat7 = Http::get('http://127.0.0.1:5000/pronosticocat7');
        $pronosticocat7=$respuestacat7->json();

        $respuestacat8 = Http::get('http://127.0.0.1:5000/pronosticocat8');
        $pronosticocat8=$respuestacat8->json();

        $respuestacat9 = Http::get('http://127.0.0.1:5000/pronosticocat9');
        $pronosticocat9=$respuestacat9->json();

        $respuestacat10 = Http::get('http://127.0.0.1:5000/pronosticocat10');
        $pronosticocat10=$respuestacat10->json();
        //API MODELO

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
