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
        $ventasYear = Sale::whereRaw('year(sale_date) = year(now())')->where('status','CONFIRMADO');
        $totalvn = (clone $ventasYear)->sum('total');
        $cantventasTotal = (clone $ventasYear)->count();

        $salesHoy = Sale::whereDate('sale_date', Carbon::today('America/La_Paz'))->where('status','CONFIRMADO');
        $totalsalesHoy = (clone $salesHoy)->sum('total');
        $cantventasHoy = (clone $salesHoy)->count();

        //---------------------------------------------
        $comprasYear = Purchase::whereRaw('year(purchase_date) = year(now())')->where('status','CONFIRMADO');
        $totalcm = (clone $comprasYear)->sum('total');
        $cantcomprasTotal = (clone $comprasYear)->count();

        $comprasHoy = Purchase::whereDate('purchase_date', Carbon::today('America/La_Paz'))->where('status','CONFIRMADO');
        $totalcomprasHoy = (clone $comprasHoy)->sum('total');
        $cantcomprasHoy = (clone $comprasHoy)->count();

        //---------------------------------------------
        $cantclientesTotal = Client::count();
        $cantclientesHoy = Client::whereDate('created_at', Carbon::today('America/La_Paz'))->count();

        //---------------------------------------------
        $cantprovsTotal = Provider::count();
        $cantprovsHoy = Provider::whereDate('created_at', Carbon::today('America/La_Paz'))->count();
        //FIN DASHBOARD

        //CONSULTAS PARA GRÁFICOS
        DB::statement("SET lc_time_names = 'es_ES'");
        $comprasmes = Purchase::whereRaw('year(purchase_date) = year(now())')->where('status', 'CONFIRMADO')->select(
            DB::raw("count(*) as count"),
            DB::raw("SUM(total) as totalmes"),
            DB::raw("DATE_FORMAT(purchase_date,'%b %Y') as mes")
        )->groupBy('mes')->take(12)->orderBy('purchase_date','ASC')->get();

        $ventasmes = Sale::whereRaw('year(sale_date) = year(now())')->where('status', 'CONFIRMADO')->select(
            DB::raw("count(*) as count"),
            DB::raw("SUM(total) as totalmes"),
            DB::raw("DATE_FORMAT(sale_date,'%b %Y') as mes")
        )->groupBy('mes')->take(12)->orderBy('sale_date','ASC')->get();

        $ventasdia = Sale::whereRaw('month(sale_date) = month(now()) AND year(sale_date) = year(now())')->where('status', 'CONFIRMADO')->select(
            DB::raw("count(*) as count"),
            DB::raw("SUM(total) as total"),
            DB::raw("DATE_FORMAT(sale_date,'%e %b') as date")
        )->groupBy('date')->take(30)->orderBy('sale_date','ASC')->get();
        $cantventasdia = $ventasdia->sum('count');

        $comprasdia = Purchase::where('status', 'CONFIRMADO')->select(
            DB::raw("count(*) as count"),
            DB::raw("SUM(total) as total"),
            DB::raw("DATE_FORMAT(purchase_date,'%e %b') as date")
        )->groupBy('date')->take(30)->orderBy('purchase_date','ASC')->get();

        $totales = DB::select('SELECT (select ifnull(sum(c.total),0) from purchases c where MONTH(c.purchase_date)=MONTH(curdate()) and YEAR(c.purchase_date)=YEAR(curdate()) and c.status="CONFIRMADO") as totalcompra,
                                      (select ifnull(sum(v.total),0) from sales v where MONTH(v.sale_date)=MONTH(curdate()) and YEAR(v.sale_date)=YEAR(curdate()) and v.status="CONFIRMADO") as totalventa');

        $productosmasvendidos=DB::select('SELECT p.code as code, p.image as imageproduct,
        sum(dv.quantity) as quantity, p.name as name, p.id as id, b.name as brand, p.stock as stock
        from products p
        inner join sale_details dv on p.id=dv.product_id
        inner join sales v on dv.sale_id=v.id
        inner join brands b on b.id=p.brand_id
        where v.status="CONFIRMADO"
        and YEAR(v.sale_date)=YEAR(curdate())
        group by p.code, p.image ,p.name, p.id, b.name, p.stock order by sum(dv.quantity) desc limit 10');

        $categoríasmasvendidas=DB::select('SELECT c.name as namecategory,
        sum(dv.quantity) as quantity from products p
        inner join sale_details dv on p.id=dv.product_id
        inner join sales v on dv.sale_id=v.id
        inner join categories c on p.category_id=c.id where v.status="CONFIRMADO" and YEAR(v.sale_date)=YEAR(curdate())
        group by c.name order by sum(dv.quantity) desc limit 5');

        $marcasmasvendidas=DB::select('SELECT m.name as namebrand,
        sum(dv.quantity) as quantity from products p
        inner join sale_details dv on p.id=dv.product_id
        inner join sales v on dv.sale_id=v.id
        inner join brands m on p.brand_id=m.id where v.status="CONFIRMADO" and YEAR(v.sale_date)=YEAR(curdate())
        group by m.name order by sum(dv.quantity) desc limit 5');

        $productosmenorstock=DB::select('SELECT name as nameproduct, image as imageproduct,
        stock from products
        where stock BETWEEN 0 AND 15
        order by stock asc limit 6');
        // FIN CONSULTAS PARA GRÁFICOS

        // PARA RETORNAR EN LA VISTA
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
}
