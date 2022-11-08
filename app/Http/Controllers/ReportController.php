<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sale;
use App\Purchase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        /*$this->middleware('can:reports.day')->only(['reports_day']);
        $this->middleware('can:reports.date')->only(['reports_date']);*/
    }

    public function reports_date(){
        $mejoresclientescant=DB::select('SELECT c.name as nameclient,
        count(v.id) as quantity, sum(v.total) as mount from sales v
        inner join clients c on v.client_id=c.id where v.status="CONFIRMADO" and YEAR(v.sale_date)=YEAR(curdate())
        group by c.name order by count(v.id) desc limit 6');

        $mejoresclientesmont=DB::select('SELECT c.name as nameclient,
        count(v.id) as quantity, sum(v.total) as mount from sales v
        inner join clients c on v.client_id=c.id where v.status="CONFIRMADO" and YEAR(v.sale_date)=YEAR(curdate())
        group by c.name order by sum(v.total) desc limit 6');

        DB::statement("SET lc_time_names = 'es_ES'");
        $salesd = Sale::whereDate('sale_date', Carbon::today('America/La_Paz'))
                ->where('status','CONFIRMADO')
                ->get();
        $totald = $salesd -> sum('total');
        $cantventasd = $salesd -> count('id');

        $salesm = Sale::whereRaw('month(sale_date) = month(now())')
                ->where('status','CONFIRMADO')
                ->get();
        $totalm = $salesm -> sum('total');
        $cantventasm = $salesm -> count('id');

        $salesy = Sale::whereRaw('year(sale_date) = year(now())')
                ->where('status','CONFIRMADO')
                ->get();
        $totaly = $salesy -> sum('total');
        $cantventasy = $salesy -> count('id');

        $fi = Carbon::now('America/La_Paz');
        $ff = Carbon::now('America/La_Paz');

        $sales = Sale::where('status','CONFIRMADO')
                    ->get();
        $total = $sales -> sum('total');
        $cantventas = $sales -> count('id');

        return view('admin.report.reports_date',
        compact(
            'salesd',
            'totald',
            'cantventasd',
            'salesm',
            'totalm',
            'cantventasm',
            'salesy',
            'totaly',
            'cantventasy',
            'sales',
            'total',
            'cantventas',
            'fi',
            'ff',
            'mejoresclientescant',
            'mejoresclientesmont')
        );
    }
    public function report_results(Request $request){

        $mejoresclientescant=DB::select('SELECT c.name as nameclient,
        count(v.id) as quantity, sum(v.total) as mount from sales v
        inner join clients c on v.client_id=c.id where v.status="CONFIRMADO" and YEAR(v.sale_date)=YEAR(curdate())
        group by c.name order by count(v.id) desc limit 5');

        $mejoresclientesmont=DB::select('SELECT c.name as nameclient,
        count(v.id) as quantity, sum(v.total) as mount from sales v
        inner join clients c on v.client_id=c.id where v.status="CONFIRMADO" and YEAR(v.sale_date)=YEAR(curdate())
        group by c.name order by sum(v.total) desc limit 5');

        DB::statement("SET lc_time_names = 'es_ES'");

        $salesd = Sale::whereDate('sale_date', Carbon::today('America/La_Paz'))
                ->where('status','CONFIRMADO')
                ->get();
        $totald = $salesd -> sum('total');
        $cantventasd = $salesd -> count('id');

        $salesm = Sale::whereRaw('month(sale_date) = month(now())')
                ->where('status','CONFIRMADO')
                ->get();
        $totalm = $salesm -> sum('total');
        $cantventasm = $salesm -> count('id');

        $salesy = Sale::whereRaw('year(sale_date) = year(now())')
                ->where('status','CONFIRMADO')
                ->get();
        $totaly = $salesy -> sum('total');
        $cantventasy = $salesy -> count('id');

        $fi = $request->fecha_ini. ' 00:00:00';
        $ff = $request->fecha_fin. ' 23:59:59';

        $sales = Sale::whereBetween('sale_date', [$fi,$ff])
                ->where('status','CONFIRMADO')
                ->get();
        $total = $sales -> sum('total');
        $cantventas = $sales -> count('id');

        return view('admin.report.reports_date',
        compact(
            'salesd',
            'totald',
            'cantventasd',
            'salesm',
            'totalm',
            'cantventasm',
            'salesy',
            'totaly',
            'cantventasy',
            'sales',
            'total',
            'cantventas',
            'fi',
            'ff',
            'mejoresclientescant',
            'mejoresclientesmont')
        );
    }
}
