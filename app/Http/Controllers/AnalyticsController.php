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
    }

    public function index()
    {

        $categories = Category::get();

        $historydatas = Historydata::get();

        //API MODELO
        $respuesta = Http::get('http://127.0.0.1:5000/pronosticofuturo');
        $pronosticos=$respuesta->json();

        $respuestafuturo = Http::get('http://127.0.0.1:5000/pronostico');
        $pronosticosfuturo=$respuesta->json();
        //API MODELO

        /*$total_datahis = Historydata::select(
            //DB::raw("count(*) as count"),
            DB::raw("category_id as category_id"),
            DB::raw("SUM(quantity) as quantity")
        )->groupBy('category_id')->orderBy('quantity','desc')->get();
        */


        $total_datahis=DB::select('SELECT c.name as namecategory,
        SUM(dh.quantity) as quantity
        from historydatas as dh
        inner join categories as c on dh.category_id=c.id
        group by c.name order by SUM(dh.quantity) desc');

        //dd($total_datahis);


        return view('admin.analytics.index', compact('pronosticos','historydatas','categories','total_datahis'));
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
