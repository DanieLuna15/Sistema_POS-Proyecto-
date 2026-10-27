<?php

namespace App\Http\Controllers;

use App\Sale;
use App\Client;
use App\Category;
use App\Product;
use App\Provider;
use App\Purchase;
use App\Quotation;
use App\CashMovement;
use App\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $today = Carbon::today('America/La_Paz');

        // 1. KPIs Principales del Año y Hoy
        $ventasYear = Sale::whereRaw('year(sale_date) = year(now())')->where('status', 'CONFIRMADO');
        $totalvn = (clone $ventasYear)->sum('total');
        $cantventasTotal = (clone $ventasYear)->count();

        $salesHoy = Sale::whereDate('sale_date', $today)->where('status', 'CONFIRMADO');
        $totalsalesHoy = (clone $salesHoy)->sum('total');
        $cantventasHoy = (clone $salesHoy)->count();

        $comprasYear = Purchase::whereRaw('year(purchase_date) = year(now())')->where('status', 'CONFIRMADO');
        $totalcm = (clone $comprasYear)->sum('total');
        $cantcomprasTotal = (clone $comprasYear)->count();

        $comprasHoy = Purchase::whereDate('purchase_date', $today)->where('status', 'CONFIRMADO');
        $totalcomprasHoy = (clone $comprasHoy)->sum('total');
        $cantcomprasHoy = (clone $comprasHoy)->count();

        $cantclientesTotal = Client::count();
        $cantclientesHoy = Client::whereDate('created_at', $today)->count();

        $cantprovsTotal = Provider::count();
        $cantprovsHoy = Provider::whereDate('created_at', $today)->count();

        // 2. Nuevos KPIs SaaS Multi-Negocio (Cuentas por Cobrar, Cotizaciones, Gastos, Utilidad)
        $cuentasPorCobrar = 0;
        $cotizacionesPendientes = 0;
        $gastosMes = 0;
        try {
            $cuentasPorCobrar = Sale::where('status', 'CONFIRMADO')
                ->where('sale_type', 'CREDITO')
                ->where('credit_status', 'PENDIENTE')
                ->sum('balance_pending');

            $cotizacionesPendientes = Quotation::where('status', 'PENDIENTE')->count();

            $gastosMes = CashMovement::where('type', 'EGRESO')
                ->whereRaw('MONTH(movement_date) = MONTH(CURDATE()) AND YEAR(movement_date) = YEAR(CURDATE())')
                ->sum('amount');
        } catch (\Throwable $e) {}

        // 3. Consultas para Gráficos
        DB::statement("SET lc_time_names = 'es_ES'");
        $comprasmes = Purchase::whereRaw('year(purchase_date) = year(now())')->where('status', 'CONFIRMADO')->select(
            DB::raw("count(*) as count"),
            DB::raw("SUM(total) as totalmes"),
            DB::raw("DATE_FORMAT(purchase_date,'%b %Y') as mes")
        )->groupBy('mes')->take(12)->orderBy('purchase_date', 'ASC')->get();

        $ventasmes = Sale::whereRaw('year(sale_date) = year(now())')->where('status', 'CONFIRMADO')->select(
            DB::raw("count(*) as count"),
            DB::raw("SUM(total) as totalmes"),
            DB::raw("DATE_FORMAT(sale_date,'%b %Y') as mes")
        )->groupBy('mes')->take(12)->orderBy('sale_date', 'ASC')->get();

        $ventasdia = Sale::whereRaw('month(sale_date) = month(now()) AND year(sale_date) = year(now())')->where('status', 'CONFIRMADO')->select(
            DB::raw("count(*) as count"),
            DB::raw("SUM(total) as total"),
            DB::raw("DATE_FORMAT(sale_date,'%e %b') as date")
        )->groupBy('date')->take(30)->orderBy('sale_date', 'ASC')->get();
        $cantventasdia = $ventasdia->sum('count');

        $comprasdia = Purchase::where('status', 'CONFIRMADO')->select(
            DB::raw("count(*) as count"),
            DB::raw("SUM(total) as total"),
            DB::raw("DATE_FORMAT(purchase_date,'%e %b') as date")
        )->groupBy('date')->take(30)->orderBy('purchase_date', 'ASC')->get();

        $companyId = Company::currentId();

        $totales = DB::select('SELECT (select ifnull(sum(c.total),0) from purchases c where (c.company_id = ? or c.company_id is null) and MONTH(c.purchase_date)=MONTH(curdate()) and YEAR(c.purchase_date)=YEAR(curdate()) and c.status="CONFIRMADO") as totalcompra,
                                      (select ifnull(sum(v.total),0) from sales v where (v.company_id = ? or v.company_id is null) and MONTH(v.sale_date)=MONTH(curdate()) and YEAR(v.sale_date)=YEAR(curdate()) and v.status="CONFIRMADO") as totalventa', [$companyId, $companyId]);

        $productosmasvendidos = DB::select('SELECT p.code as code, p.image as imageproduct,
        sum(dv.quantity) as quantity, p.name as name, p.id as id, b.name as brand, p.stock as stock
        from products p
        inner join sale_details dv on p.id=dv.product_id
        inner join sales v on dv.sale_id=v.id
        inner join brands b on b.id=p.brand_id
        where v.status="CONFIRMADO"
        and (v.company_id = ? or v.company_id is null)
        and YEAR(v.sale_date)=YEAR(curdate())
        group by p.code, p.image ,p.name, p.id, b.name, p.stock order by sum(dv.quantity) desc limit 10', [$companyId]);

        $categoríasmasvendidas = DB::select('SELECT c.name as namecategory,
        sum(dv.quantity) as quantity from products p
        inner join sale_details dv on p.id=dv.product_id
        inner join sales v on dv.sale_id=v.id
        inner join categories c on p.category_id=c.id where v.status="CONFIRMADO" and (v.company_id = ? or v.company_id is null) and YEAR(v.sale_date)=YEAR(curdate())
        group by c.name order by sum(dv.quantity) desc limit 5', [$companyId]);

        $marcasmasvendidas = DB::select('SELECT m.name as namebrand,
        sum(dv.quantity) as quantity from products p
        inner join sale_details dv on p.id=dv.product_id
        inner join sales v on dv.sale_id=v.id
        inner join brands m on p.brand_id=m.id where v.status="CONFIRMADO" and (v.company_id = ? or v.company_id is null) and YEAR(v.sale_date)=YEAR(curdate())
        group by m.name order by sum(dv.quantity) desc limit 5', [$companyId]);

        $productosmenorstock = DB::select('SELECT id, name as nameproduct, image as imageproduct,
        stock from products
        where (company_id = ? or company_id is null) and stock BETWEEN 0 AND 15
        order by stock asc limit 6', [$companyId]);

        return view('home', compact(
            'comprasmes',
            'ventasmes',
            'ventasdia',
            'cantventasdia',
            'comprasdia',
            'cantventasTotal',
            'totalvn',
            'totalsalesHoy',
            'cantventasHoy',
            'cantcomprasTotal',
            'totalcm',
            'totalcomprasHoy',
            'cantcomprasHoy',
            'cantclientesTotal',
            'cantclientesHoy',
            'cantprovsTotal',
            'cantprovsHoy',
            'cuentasPorCobrar',
            'cotizacionesPendientes',
            'gastosMes',
            'totales',
            'productosmasvendidos',
            'categoríasmasvendidas',
            'marcasmasvendidas',
            'productosmenorstock'
        ));
    }
}
