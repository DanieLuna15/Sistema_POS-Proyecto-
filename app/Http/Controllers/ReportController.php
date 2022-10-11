<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sale;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        /*$this->middleware('can:reports.day')->only(['reports_day']);
        $this->middleware('can:reports.date')->only(['reports_date']);*/
    }

    public function reports_day(){
        $sales = Sale::whereDate('sale_date', Carbon::today('America/La_Paz'))
                    ->where('status','CONFIRMADO')
                    ->get();
        $total = $sales -> sum('total');
        $cantventas = $sales -> count('id');

        return view('admin.report.reports_day', compact('sales','total','cantventas'));
    }

    public function reports_month(){
        $sales = Sale::whereRaw('month(sale_date) = month(now())')
                    ->where('status','CONFIRMADO')
                    ->get();
        $total = $sales -> sum('total');
        $cantventas = $sales -> count('id');

        return view('admin.report.reports_day', compact('sales','total','cantventas'));
    }

    public function reports_year(){
        $sales = Sale::whereRaw('year(sale_date) = year(now())')
                    ->where('status','CONFIRMADO')
                    ->get();
        $total = $sales -> sum('total');
        $cantventas = $sales -> count('id');

        return view('admin.report.reports_day', compact('sales','total','cantventas'));
    }

    public function reports_date(){
        $fi = Carbon::now('America/La_Paz');
        $ff = Carbon::now('America/La_Paz');

        //$fi = $fi->format('d/m/Y');
        //$ff = $ff->format('d/m/Y');

        $sales = Sale::whereDate('sale_date', Carbon::today('America/La_Paz'))
                    ->where('status','CONFIRMADO')
                    ->get();
        $total = $sales -> sum('total');
        $cantventas = $sales -> count('id');

        return view('admin.report.reports_date', compact('sales','total','cantventas','fi','ff'));
    }
    public function report_results(Request $request){
        $fi = $request->fecha_ini. ' 00:00:00';
        $ff = $request->fecha_fin. ' 23:59:59';

        $sales = Sale::whereBetween('sale_date', [$fi,$ff])
                ->where('status','CONFIRMADO')
                ->get();
        $total = $sales -> sum('total');
        $cantventas = $sales -> count('id');


        return view('admin.report.reports_date', compact('sales','total','cantventas','fi','ff'));
    }
}
