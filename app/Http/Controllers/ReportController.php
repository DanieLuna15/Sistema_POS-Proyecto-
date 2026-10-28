<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sale;
use App\Company;
use App\Branch;
use App\Purchase;
use App\CashMovement;
use App\Product;
use App\Provider;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
//Para sweet alert en Repo
use RealRashid\SweetAlert\Facades\Alert;
class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:reports.date')->only(['reports_date']);
    }

    public function reports_date(){
        $companyId = Company::currentId();
        $mejoresclientescant=DB::select('SELECT c.name as nameclient,
        count(v.id) as quantity, sum(v.total) as mount from sales v
        inner join clients c on v.client_id=c.id where v.status="CONFIRMADO" and (v.company_id = ? or v.company_id is null) and YEAR(v.sale_date)=YEAR(curdate())
        group by c.name order by count(v.id) desc limit 6', [$companyId]);

        $mejoresclientesmont=DB::select('SELECT c.name as nameclient,
        count(v.id) as quantity, sum(v.total) as mount from sales v
        inner join clients c on v.client_id=c.id where v.status="CONFIRMADO" and (v.company_id = ? or v.company_id is null) and YEAR(v.sale_date)=YEAR(curdate())
        group by c.name order by sum(v.total) desc limit 6', [$companyId]);

        DB::statement("SET lc_time_names = 'es_ES'");
        $salesd = Sale::whereDate('sale_date', Carbon::today('America/La_Paz'))
                ->where('status','CONFIRMADO')
                ->get();
        $totald = $salesd -> sum('total');
        $cantventasd = $salesd -> count('id');

        $salesm = Sale::whereRaw('month(sale_date) = month(now()) AND year(sale_date) = year(now())')
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

        $salescanc = Sale::whereRaw('year(sale_date) = year(now())')
                ->where('status','CANCELADO')
                ->get();
        $totalcanc = $salescanc -> sum('total');
        $cantventascanc = $salescanc -> count('id');

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
            'salescanc',
            'totalcanc',
            'cantventascanc',
            'fi',
            'ff',
            'mejoresclientescant',
            'mejoresclientesmont')
        );
    }

    public function exportar()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SalesExport, 'reporte_ventas_' . date('Y_m_d') . '.xlsx');
    }

    public function report_results(Request $request){
        $companyId = Company::currentId();
        $mejoresclientescant=DB::select('SELECT c.name as nameclient,
        count(v.id) as quantity, sum(v.total) as mount from sales v
        inner join clients c on v.client_id=c.id where v.status="CONFIRMADO" and (v.company_id = ? or v.company_id is null) and YEAR(v.sale_date)=YEAR(curdate())
        group by c.name order by count(v.id) desc limit 5', [$companyId]);

        $mejoresclientesmont=DB::select('SELECT c.name as nameclient,
        count(v.id) as quantity, sum(v.total) as mount from sales v
        inner join clients c on v.client_id=c.id where v.status="CONFIRMADO" and (v.company_id = ? or v.company_id is null) and YEAR(v.sale_date)=YEAR(curdate())
        group by c.name order by sum(v.total) desc limit 5', [$companyId]);

        DB::statement("SET lc_time_names = 'es_ES'");

        $salesd = Sale::whereDate('sale_date', Carbon::today('America/La_Paz'))
                ->where('status','CONFIRMADO')
                ->get();
        $totald = $salesd -> sum('total');
        $cantventasd = $salesd -> count('id');

        $salesm = Sale::whereRaw('month(sale_date) = month(now()) AND year(sale_date) = year(now())')
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

        $salescanc = Sale::whereRaw('year(sale_date) = year(now())')
                ->where('status','CANCELADO')
                ->get();
        $totalcanc = $salescanc -> sum('total');
        $cantventascanc = $salescanc -> count('id');


        Alert::toast('Reporte Generado.', 'info');
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
            'salescanc',
            'totalcanc',
            'cantventascanc',
            'fi',
            'ff',
            'mejoresclientescant',
            'mejoresclientesmont')
        );
    }

    /**
     * Módulo de Análisis Financiero: Utilidades, Márgenes, Compras y Rentabilidad (Global y por Sucursal)
     */
    public function profits(Request $request)
    {
        $companyId = Company::currentId();
        $activeCompany = Company::current();
        $sym = $activeCompany ? ($activeCompany->currency_symbol ?? 'Bs.') : 'Bs.';
        $branches = Branch::where('status', 'ACTIVO')->get();

        $selectedBranchId = $request->get('branch_id', 'ALL');
        if (empty($selectedBranchId)) {
            $selectedBranchId = 'ALL';
        }
        $period = $request->get('period', 'MES'); // HOY, SEMANA, MES, TRIMESTRE, ANIO, CUSTOM

        $today = Carbon::now('America/La_Paz');

        switch ($period) {
            case 'HOY':
                $startDate = $today->copy()->startOfDay();
                $endDate = $today->copy()->endOfDay();
                $periodLabel = "Hoy (" . $today->format('d/m/Y') . ")";
                break;
            case 'SEMANA':
                $startDate = $today->copy()->startOfWeek();
                $endDate = $today->copy()->endOfWeek();
                $periodLabel = "Esta Semana (" . $startDate->format('d/m') . " al " . $endDate->format('d/m/Y') . ")";
                break;
            case 'TRIMESTRE':
                $startDate = $today->copy()->subMonths(3)->startOfDay();
                $endDate = $today->copy()->endOfDay();
                $periodLabel = "Últimos 3 Meses (" . $startDate->format('d/m/Y') . " al " . $endDate->format('d/m/Y') . ")";
                break;
            case 'ANIO':
                $startDate = $today->copy()->startOfYear();
                $endDate = $today->copy()->endOfYear();
                $periodLabel = "Año " . $today->year;
                break;
            case 'CUSTOM':
                $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : $today->copy()->startOfMonth();
                $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : $today->copy()->endOfDay();
                $periodLabel = "Del " . $startDate->format('d/m/Y') . " al " . $endDate->format('d/m/Y');
                break;
            case 'MES':
            default:
                $startDate = $today->copy()->startOfMonth();
                $endDate = $today->copy()->endOfDay();
                $periodLabel = "Mes Actual (" . $today->translatedFormat('F Y') . ")";
                break;
        }

        // 1. Consulta de Ventas y Costos de Venta (COGS)
        $salesQuery = Sale::where('status', 'CONFIRMADO')
            ->whereBetween('sale_date', [$startDate, $endDate]);

        if ($selectedBranchId && $selectedBranchId !== 'ALL') {
            $salesQuery->where('branch_id', $selectedBranchId);
        }

        $sales = $salesQuery->with(['saleDetails.product.category', 'branch'])->get();

        $totalSales = $sales->sum('total');
        $totalOrdersCount = $sales->count();
        $totalCogs = 0; // Costo de insumos/receta vendida

        // Rentabilidad por producto/combo
        $productProfits = [];

        foreach ($sales as $sale) {
            foreach ($sale->saleDetails as $detail) {
                $prod = $detail->product;
                $prodId = $detail->product_id;
                $qty = floatval($detail->quantity);
                $revenue = (floatval($detail->price) * $qty) - floatval($detail->discount ?? 0);
                $unitCost = $prod ? floatval($prod->purchase_price) : 0;
                $cost = $unitCost * $qty;
                $profit = $revenue - $cost;

                $totalCogs += $cost;

                if (!isset($productProfits[$prodId])) {
                    $productProfits[$prodId] = [
                        'product_id' => $prodId,
                        'name' => $prod ? $prod->name : 'Ítem eliminado',
                        'item_type' => $prod ? $prod->item_type : 'PRODUCTO',
                        'category' => $prod && $prod->category ? $prod->category->name : 'Sin categoría',
                        'quantity' => 0,
                        'revenue' => 0,
                        'cost' => 0,
                        'profit' => 0,
                        'image' => $prod ? $prod->image : null,
                    ];
                }

                $productProfits[$prodId]['quantity'] += $qty;
                $productProfits[$prodId]['revenue'] += $revenue;
                $productProfits[$prodId]['cost'] += $cost;
                $productProfits[$prodId]['profit'] += $profit;
            }
        }

        // Ordenar productos por mayor utilidad neta en Bs
        uasort($productProfits, function ($a, $b) {
            return $b['profit'] <=> $a['profit'];
        });

        $grossProfit = $totalSales - $totalCogs;
        $grossMarginPct = $totalSales > 0 ? round(($grossProfit / $totalSales) * 100, 1) : 0;

        // 2. Consulta de Compras a Proveedores (Abastecimiento de Insumos)
        $purchasesQuery = Purchase::where('status', 'CONFIRMADO')
            ->whereBetween('purchase_date', [$startDate, $endDate]);

        if ($selectedBranchId && $selectedBranchId !== 'ALL') {
            $purchasesQuery->where('branch_id', $selectedBranchId);
        }

        $purchases = $purchasesQuery->with(['provider', 'branch'])->get();
        $totalPurchases = $purchases->sum('total');

        // Compras agrupadas por proveedor
        $providerPurchases = [];
        foreach ($purchases as $pur) {
            $provName = $pur->provider ? $pur->provider->name : 'Proveedor General';
            if (!isset($providerPurchases[$provName])) {
                $providerPurchases[$provName] = [
                    'name' => $provName,
                    'count' => 0,
                    'total' => 0,
                ];
            }
            $providerPurchases[$provName]['count']++;
            $providerPurchases[$provName]['total'] += floatval($pur->total);
        }
        uasort($providerPurchases, function ($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        // 3. Gastos Operativos de Caja Chica (Egresos)
        $expensesQuery = CashMovement::where('type', 'EGRESO')
            ->whereBetween('movement_date', [$startDate, $endDate]);

        if ($selectedBranchId && $selectedBranchId !== 'ALL') {
            $expensesQuery->where('branch_id', $selectedBranchId);
        }

        $expenses = $expensesQuery->with(['branch'])->get();
        $totalExpenses = $expenses->sum('amount');

        // Gastos agrupados por categoría
        $expensesByCategory = $expenses->groupBy('category')->map(function ($items, $cat) {
            return [
                'category' => $cat ?: 'Otros Gastos',
                'total' => $items->sum('amount'),
                'count' => $items->count(),
            ];
        })->sortByDesc('total');

        // 4. Utilidad Neta Real
        $netProfit = $grossProfit - $totalExpenses;
        $netMarginPct = $totalSales > 0 ? round(($netProfit / $totalSales) * 100, 1) : 0;

        // 5. Análisis Comparativo por Sucursales (Global y por Sucursal)
        $branchComparison = [];
        foreach ($branches as $br) {
            $brSales = Sale::where('status', 'CONFIRMADO')
                ->where('branch_id', $br->id)
                ->whereBetween('sale_date', [$startDate, $endDate])
                ->with(['saleDetails.product'])
                ->get();

            $brTotalSales = $brSales->sum('total');
            $brCogs = 0;
            foreach ($brSales as $s) {
                foreach ($s->saleDetails as $d) {
                    $uCost = $d->product ? floatval($d->product->purchase_price) : 0;
                    $brCogs += ($uCost * floatval($d->quantity));
                }
            }

            $brExpenses = CashMovement::where('type', 'EGRESO')
                ->where('branch_id', $br->id)
                ->whereBetween('movement_date', [$startDate, $endDate])
                ->sum('amount');

            $brGrossProfit = $brTotalSales - $brCogs;
            $brGrossMargin = $brTotalSales > 0 ? round(($brGrossProfit / $brTotalSales) * 100, 1) : 0;
            $brNetProfit = $brGrossProfit - $brExpenses;
            $brNetMargin = $brTotalSales > 0 ? round(($brNetProfit / $brTotalSales) * 100, 1) : 0;

            $branchComparison[] = [
                'branch_id' => $br->id,
                'name' => $br->name,
                'is_main' => $br->is_main,
                'sales_count' => $brSales->count(),
                'total_sales' => $brTotalSales,
                'cogs' => $brCogs,
                'gross_profit' => $brGrossProfit,
                'gross_margin' => $brGrossMargin,
                'expenses' => $brExpenses,
                'net_profit' => $brNetProfit,
                'net_margin' => $brNetMargin,
                'contribution_pct' => $totalSales > 0 ? round(($brTotalSales / $totalSales) * 100, 1) : 0,
            ];
        }

        // 6. Serie Diaria para Gráfico de Líneas/Barras
        DB::statement("SET lc_time_names = 'es_ES'");
        $chartData = [];
        $currentCursor = $startDate->copy();
        $daysCount = $startDate->diffInDays($endDate) + 1;

        if ($daysCount <= 31) {
            while ($currentCursor->lte($endDate)) {
                $dayStr = $currentCursor->format('Y-m-d');
                $dayLabel = $currentCursor->format('d M');

                $daySales = $sales->filter(function ($s) use ($dayStr) {
                    return Carbon::parse($s->sale_date)->format('Y-m-d') === $dayStr;
                });
                $daySaleTotal = $daySales->sum('total');

                $dayCogs = 0;
                foreach ($daySales as $s) {
                    foreach ($s->saleDetails as $d) {
                        $uCost = $d->product ? floatval($d->product->purchase_price) : 0;
                        $dayCogs += ($uCost * floatval($d->quantity));
                    }
                }

                $chartData[] = [
                    'date' => $dayLabel,
                    'sales' => round($daySaleTotal, 2),
                    'cogs' => round($dayCogs, 2),
                    'profit' => round($daySaleTotal - $dayCogs, 2),
                ];

                $currentCursor->addDay();
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.report.partials.profits_content', compact(
                    'branches',
                    'selectedBranchId',
                    'period',
                    'periodLabel',
                    'startDate',
                    'endDate',
                    'totalSales',
                    'totalOrdersCount',
                    'totalCogs',
                    'grossProfit',
                    'grossMarginPct',
                    'totalPurchases',
                    'providerPurchases',
                    'totalExpenses',
                    'expensesByCategory',
                    'netProfit',
                    'netMarginPct',
                    'productProfits',
                    'branchComparison',
                    'chartData',
                    'sym',
                    'activeCompany'
                ))->render(),
                'chartData' => $chartData,
                'period' => $period,
                'periodLabel' => $periodLabel,
                'selectedBranchId' => $selectedBranchId,
                'selectedBranchName' => $selectedBranchId === 'ALL'
                    ? '🌐 Consolidado Global (Todas las Sucursales)'
                    : optional($branches->firstWhere('id', $selectedBranchId))->name,
                'isMain' => $selectedBranchId === 'ALL' ? false : (bool)optional($branches->firstWhere('id', $selectedBranchId))->is_main,
                'startDate' => $startDate->format('Y-m-d'),
                'endDate' => $endDate->format('Y-m-d'),
                'sym' => $sym,
            ]);
        }

        return view('admin.report.profits', compact(
            'branches',
            'selectedBranchId',
            'period',
            'periodLabel',
            'startDate',
            'endDate',
            'totalSales',
            'totalOrdersCount',
            'totalCogs',
            'grossProfit',
            'grossMarginPct',
            'totalPurchases',
            'providerPurchases',
            'totalExpenses',
            'expensesByCategory',
            'netProfit',
            'netMarginPct',
            'productProfits',
            'branchComparison',
            'chartData',
            'sym',
            'activeCompany'
        ));
    }
}
