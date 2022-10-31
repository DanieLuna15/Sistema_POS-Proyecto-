<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Purchase;
use Carbon\Carbon;

class ReportcmController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        /*$this->middleware('can:reports.day')->only(['reports_day']);
        $this->middleware('can:reports.date')->only(['reports_date']);*/
    }
    public function reportscm_daycm(){
        $purchases = Purchase::whereDate('purchase_date', Carbon::today('America/La_Paz'))
                    ->where('status','CONFIRMADO')
                    ->get();
        $totalcm = $purchases -> sum('total');
        $cantcompras = $purchases -> count('id');

        return view('admin.report.reportscm_daycm', compact('purchases','totalcm','cantcompras'));
    }
    public function reportscm_datecm(){
        $fi = Carbon::now('America/La_Paz');
        $ff = Carbon::now('America/La_Paz');

        $purchases = Purchase::where('status','CONFIRMADO')
                    ->get();
        $totalcm = $purchases -> sum('total');
        $cantcompras = $purchases -> count('id');

        return view('admin.report.reportscm_datecm', compact('purchases','totalcm','cantcompras','fi','ff'));
    }
    public function reportcm_resultscm(Request $request){
        $fi = $request->fecha_ini. ' 00:00:00';
        $ff = $request->fecha_fin. ' 23:59:59';

        $purchases = Purchase::whereBetween('purchase_date', [$fi,$ff])
                ->where('status','CONFIRMADO')
                ->get();
        $totalcm = $purchases -> sum('total');
        $cantcompras = $purchases -> count('id');

        return view('admin.report.reportscm_datecm', compact('purchases','totalcm','cantcompras','fi','ff'));
    }
}
