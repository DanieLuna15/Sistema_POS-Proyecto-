<?php

namespace App\Http\Controllers;

use App\Sale;
use App\Client;
use App\Category;
use App\Product;
use App\Provider;
use App\Purchase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Http;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        //PARA DASHBOARD
        //---------------------------------------------
        $salesTotal = Sale::where('status','CONFIRMADO')
        ->get();
        $totalvn = $salesTotal -> sum('total');
        $cantventasTotal = $salesTotal -> count('id');

        $salesHoy = Sale::whereDate('sale_date', Carbon::today('America/La_Paz'))
                ->where('status','CONFIRMADO')
                ->get();

        $salesMes = Sale::whereRaw('month(sale_date) = month(now())')
                ->where('status','CONFIRMADO')
                ->get();
        $totalvnmes = $salesMes -> sum('total');

        $totalsalesHoy = $salesHoy -> sum('total');
        $cantventasHoy = $salesHoy -> count('id');
        //---------------------------------------------
        $comprasTotal = Purchase::where('status','CONFIRMADO')
                ->get();
        $totalcm = $comprasTotal -> sum('total');
        $cantcomprasTotal = $comprasTotal -> count('id');

        $comprasHoy = Purchase::whereDate('purchase_date', Carbon::today('America/La_Paz'))
                ->where('status','CONFIRMADO')
                ->get();
        $totalcomprasHoy = $comprasHoy -> sum('total');
        $cantcomprasHoy = $comprasHoy -> count('id');
        //---------------------------------------------
        $clientesTotal = Client::get();
        $cantclientesTotal = $clientesTotal -> count('id');

        $clientesHoy = Client::whereDate('created_at', Carbon::today('America/La_Paz'))
                ->get();
        $totalclientesHoy = $clientesHoy -> sum('total');
        $cantclientesHoy = $clientesHoy -> count('id');
        //---------------------------------------------
        $provsTotal = Provider::get();
        $cantprovsTotal = $provsTotal -> count('id');

        $provsHoy = Provider::whereDate('created_at', Carbon::today('America/La_Paz'))
                ->get();
        $totalprovsHoyHoy = $provsHoy -> sum('total');
        $cantprovsHoy = $provsHoy -> count('id');
        //FIN DASHBOARD

        //CONSULTAS PARA GRÁFICOS
        DB::statement("SET lc_time_names = 'es_ES'");
        $comprasmes = Purchase::where('status', 'CONFIRMADO')->select(
            DB::raw("count(*) as count"),
            DB::raw("SUM(total) as totalmes"),
            DB::raw("DATE_FORMAT(purchase_date,'%b %Y') as mes")
        )->groupBy('mes')->take(12)->orderBy('purchase_date','ASC')->get();
        //dd($comprasmes);

        DB::statement("SET lc_time_names = 'es_ES'");
        $ventasmes = Sale::where('status', 'CONFIRMADO')->select(
            DB::raw("count(*) as count"),
            DB::raw("SUM(total) as totalmes"),
            DB::raw("DATE_FORMAT(sale_date,'%b %Y') as mes")
        )->groupBy('mes')->take(12)->orderBy('sale_date','ASC')->get();
        //dd($ventasmes);

        $ventasdia = Sale::whereRaw('year(sale_date) = year(now())')->where('status', 'CONFIRMADO')->select(
            DB::raw("count(*) as count"),
            DB::raw("SUM(total) as total"),
            DB::raw("DATE_FORMAT(sale_date,'%e %b') as date")
        )->groupBy('date')->take(30)->orderBy('sale_date','ASC')->get();
        $cantventasdia = $ventasdia -> sum('count');

        $comprasdia = Purchase::where('status', 'CONFIRMADO')->select(
            DB::raw("count(*) as count"),
            DB::raw("SUM(total) as total"),
            DB::raw("DATE_FORMAT(purchase_date,'%e %b') as date")
        )->groupBy('date')->take(30)->orderBy('purchase_date','ASC')->get();

        $totales=DB::select('SELECT (select ifnull(sum(c.total),0) from purchases c where DATE(MONTH(c.purchase_date))=MONTH(curdate()) and c.status="CONFIRMADO") as totalcompra,
                                    (select ifnull(sum(v.total),0) from sales v where DATE(MONTH(v.sale_date))=MONTH(curdate()) and v.status="CONFIRMADO") as totalventa');

        $productosmasvendidos=DB::select('SELECT p.code as code,
        sum(dv.quantity) as quantity, p.name as name , p.id as id , p.stock as stock from products p
        inner join sale_details dv on p.id=dv.product_id
        inner join sales v on dv.sale_id=v.id where v.status="CONFIRMADO"
        and MONTH(v.sale_date)=MONTH(curdate())
        group by p.code ,p.name, p.id , p.stock order by sum(dv.quantity) desc limit 10');

        $categoríasmasvendidas=DB::select('SELECT c.name as namecategory,
        sum(dv.quantity) as quantity from products p
        inner join sale_details dv on p.id=dv.product_id
        inner join sales v on dv.sale_id=v.id
        inner join categories c on p.category_id=c.id where v.status="CONFIRMADO" and MONTH(v.sale_date)=MONTH(curdate())
        group by c.name order by sum(dv.quantity) desc limit 5');

        $marcasmasvendidas=DB::select('SELECT m.name as namebrand,
        sum(dv.quantity) as quantity from products p
        inner join sale_details dv on p.id=dv.product_id
        inner join sales v on dv.sale_id=v.id
        inner join brands m on p.brand_id=m.id where v.status="CONFIRMADO" and MONTH(v.sale_date)=MONTH(curdate())
        group by m.name order by sum(dv.quantity) desc limit 5');

        $productosmenorstock=DB::select('SELECT name as nameproduct,
        stock from products
        where stock BETWEEN 0 AND 15
        order by stock desc limit 6');

        //dd($productosmasvendidos);
        //dd($ventasdia);
        return view('home', compact(
            'comprasmes',
            'ventasmes',
            'ventasdia',
            'cantventasdia',
            'comprasdia',
            'cantventasTotal',
            'totalvn',
            'cantventasHoy',
            'cantcomprasTotal',
            'totalcm',
            'cantcomprasHoy',
            'cantclientesTotal',
            'cantclientesHoy',
            'cantprovsTotal',
            'cantprovsHoy',
            'totales',
            'productosmasvendidos',
            'categoríasmasvendidas',
            'marcasmasvendidas',
            'productosmenorstock')
        );
    }

    public function index1()
    {
        /*
        $comprasmes = Purchase::where('status', 'VALID')->select(
            DB::raw("count(*) as count"),
            DB::raw("SUM(total) as totalmes"),
            DB::raw("DATE_FORMAT(purchase_date,'%M %Y') as mes")
        )->groupBy('mes')->take(12)->get();
        $orders_of_the_day = Order::where('order_date', Carbon::now()->format('Y-m-d'))->take(5)->get();
        $orders_of_the_day_status = Order::where('order_date', Carbon::now()->format('Y-m-d'))
        ->select(
            DB::raw("count(*) as count"),
            DB::raw("shipping_status as status")
        )->groupBy('status')->get();
        $ventasmes = Sale::where('status', 'VALID')->select(
            DB::raw("count(*) as count"),
            DB::raw("SUM(total) as totalmes"),
            DB::raw("DATE_FORMAT(sale_date,'%M %Y') as mes")
        )->groupBy('mes')->take(12)->get();
        $ventasdia = Sale::where('status', 'VALID')->select(
            DB::raw("count(*) as count"),
            DB::raw("SUM(total) as total"),
            DB::raw("DATE_FORMAT(sale_date,'%D %M %Y') as date")
        )->groupBy('date')->take(30)->get();
        $most_ordered_products = OrderDetail::select(
            DB::raw("SUM(quantity) as total"),
            DB::raw("product_id as product_id")
        )->groupBy('product_id')->take(12)->get();
        $order_mes = Order::where('order_date', Carbon::now()->subdays(30)->format('Y-m-d'))->select(
            DB::raw("count(*) as count"),
            DB::raw("shipping_status as status")
        )->groupBy('status')->get();
        $totales=DB::select('SELECT (select ifnull(sum(c.total),0) from purchases c where DATE(MONTH(c.purchase_date))=MONTH(curdate()) and c.status="VALID") as totalcompra, (select ifnull(sum(v.total),0) from sales v where DATE(MONTH(v.sale_date))=MONTH(curdate()) and v.status="VALID") as totalventa');
        $productosvendidos=DB::select('SELECT p.code as code,
        sum(dv.quantity) as quantity, p.name as name , p.id as id , p.stock as stock from products p
        inner join sale_details dv on p.id=dv.product_id
        inner join sales v on dv.sale_id=v.id where v.status="VALID"
        and MONTH(v.sale_date)=MONTH(curdate())
        group by p.code ,p.name, p.id , p.stock order by sum(dv.quantity) desc limit 10');
        return view('home', compact(
            'comprasmes',
            'ventasmes',
            'ventasdia',
            'totales',
            'productosvendidos',
            'order_mes',
            'most_ordered_products',
            'orders_of_the_day',
            'orders_of_the_day_status')
        );

        $total_datahis = Order::where('order_date', Carbon::now()->subdays(30)->format('Y-m-d'))->select(
            DB::raw("count(*) as count"),
            DB::raw("shipping_status as status")
        )->groupBy('status')->get();

        $order_mes = Order::where('order_date', Carbon::now()->subdays(30)->format('Y-m-d'))->select(
            DB::raw("count(*) as count"),
            DB::raw("shipping_status as status")
        )->groupBy('status')->get();


        $total_datahis = Historydata::select(
            DB::raw("SUM(quantity) as quantity"),
        )->groupBy('category_id')->get();

        dd($total_datahis);*/
    }
}
