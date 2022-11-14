<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Purchase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
//Para sweet alert en Repo
use RealRashid\SweetAlert\Facades\Alert;
class ReportcmController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:reportscm.datecm')->only(['reportscm_datecm']);
    }

    public function reportscm_datecm(){

        $mejoresprovcantcompras=DB::select('SELECT p.name as nameprov,count(c.id) as quantity, sum(c.total) as mount, sum(dc.quantity) as quantityprod
        from (purchases c inner join providers p on c.provider_id=p.id) inner join purchase_details as dc on c.id=dc.purchase_id
        where c.status="CONFIRMADO" and YEAR(c.purchase_date)=YEAR(curdate())
        group by p.name order by count(c.id) desc limit 6');

		$mejoresprovcantproductos=DB::select('SELECT p.name as nameprov,count(c.id) as quantity, sum(c.total) as mount, sum(dc.quantity) as quantityprod
        from (purchases c inner join providers p on c.provider_id=p.id) inner join purchase_details as dc on c.id=dc.purchase_id
        where c.status="CONFIRMADO" and YEAR(c.purchase_date)=YEAR(curdate())
        group by p.name order by sum(dc.quantity) desc limit 6');

        DB::statement("SET lc_time_names = 'es_ES'");
        $purchasesd = Purchase::whereDate('purchase_date', Carbon::today('America/La_Paz'))
                ->where('status','CONFIRMADO')
                ->get();
        $totald= $purchasesd -> sum('total');
        $cantcomprasd = $purchasesd -> count('id');

        $purchasesm = Purchase::whereRaw('month(purchase_date) = month(now())')
                ->where('status','CONFIRMADO')
                ->get();
        $totalm= $purchasesm -> sum('total');
        $cantcomprasm = $purchasesm -> count('id');

        $purchasesy = Purchase::whereRaw('year(purchase_date) = year(now())')
                ->where('status','CONFIRMADO')
                ->get();
        $totaly= $purchasesy -> sum('total');
        $cantcomprasy = $purchasesy -> count('id');

        $fi = Carbon::now('America/La_Paz');
        $ff = Carbon::now('America/La_Paz');

        $purchases = Purchase::where('status','CONFIRMADO')
                    ->get();
        $total = $purchases -> sum('total');
        $cantcompras = $purchases -> count('id');

        $purchasescanc = Purchase::whereRaw('year(purchase_date) = year(now())')
                ->where('status','CANCELADO')
                ->get();
        $totalcanc = $purchasescanc -> sum('total');
        $cantcomprascanc = $purchasescanc -> count('id');

        return view('admin.report.reportscm_datecm',
        compact(
            'purchasesd',
            'totald',
            'cantcomprasd',
            'purchasesm',
            'totalm',
            'cantcomprasm',
            'purchasesy',
            'totaly',
            'cantcomprasy',
            'purchases',
            'total',
            'cantcompras',
            'purchasescanc',
            'totalcanc',
            'cantcomprascanc',
            'fi',
            'ff',
            'mejoresprovcantcompras',
            'mejoresprovcantproductos')
        );
    }

    public function reportcm_resultscm(Request $request){

        $mejoresprovcantcompras=DB::select('SELECT p.name as nameprov,count(c.id) as quantity, sum(c.total) as mount, sum(dc.quantity) as quantityprod
        from (purchases c inner join providers p on c.provider_id=p.id) inner join purchase_details as dc on c.id=dc.purchase_id
        where c.status="CONFIRMADO" and YEAR(c.purchase_date)=YEAR(curdate())
        group by p.name order by count(c.id) desc limit 6');

		$mejoresprovcantproductos=DB::select('SELECT p.name as nameprov,count(c.id) as quantity, sum(c.total) as mount, sum(dc.quantity) as quantityprod
        from (purchases c inner join providers p on c.provider_id=p.id) inner join purchase_details as dc on c.id=dc.purchase_id
        where c.status="CONFIRMADO" and YEAR(c.purchase_date)=YEAR(curdate())
        group by p.name order by sum(dc.quantity) desc limit 6');

        DB::statement("SET lc_time_names = 'es_ES'");

        $purchasesd = Purchase::whereDate('purchase_date', Carbon::today('America/La_Paz'))
                ->where('status','CONFIRMADO')
                ->get();
        $totald= $purchasesd -> sum('total');
        $cantcomprasd = $purchasesd -> count('id');

        $purchasesm = Purchase::whereRaw('month(purchase_date) = month(now())')
                ->where('status','CONFIRMADO')
                ->get();
        $totalm= $purchasesm -> sum('total');
        $cantcomprasm = $purchasesm -> count('id');

        $purchasesy = Purchase::whereRaw('year(purchase_date) = year(now())')
                ->where('status','CONFIRMADO')
                ->get();
        $totaly= $purchasesy -> sum('total');
        $cantcomprasy = $purchasesy -> count('id');

        $fi = $request->fecha_ini. ' 00:00:00';
        $ff = $request->fecha_fin. ' 23:59:59';

        $purchases = Purchase::whereBetween('purchase_date', [$fi,$ff])
                ->where('status','CONFIRMADO')
                ->get();
        $total = $purchases -> sum('total');
        $cantcompras = $purchases -> count('id');

        $purchasescanc = Purchase::whereRaw('year(purchase_date) = year(now())')
                ->where('status','CANCELADO')
                ->get();
        $totalcanc = $purchasescanc -> sum('total');
        $cantcomprascanc = $purchasescanc -> count('id');

        Alert::toast('Reporte Generado.', 'info');
        return view('admin.report.reportscm_datecm',
        compact(
            'purchasesd',
            'totald',
            'cantcomprasd',
            'purchasesm',
            'totalm',
            'cantcomprasm',
            'purchasesy',
            'totaly',
            'cantcomprasy',
            'purchases',
            'total',
            'cantcompras',
            'purchasescanc',
            'totalcanc',
            'cantcomprascanc',
            'fi',
            'ff',
            'mejoresprovcantcompras',
            'mejoresprovcantproductos')
        );
    }
}
