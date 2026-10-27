<?php

namespace App\Http\Controllers;

use App\CashRegister;
use App\CashMovement;
use App\Branch;
use App\Company;
use App\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use RealRashid\SweetAlert\Facades\Alert;

class CashRegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Panel Principal de Control de Caja, Movimientos de Caja Chica e Historial de Turnos
     */
    public function index()
    {
        $openRegister = CashRegister::activeOpen();
        if ($openRegister) {
            $openRegister->recalculateTotals();
        }

        $registers = CashRegister::with(['user', 'closedBy', 'branch'])
            ->orderBy('opening_date', 'DESC')
            ->take(30)
            ->get();

        $movements = CashMovement::with(['user', 'branch', 'cashRegister'])
            ->orderBy('movement_date', 'DESC')
            ->take(50)
            ->get();

        $paymentMethods = PaymentMethod::active()->get();

        return view('admin.cash_register.index', compact('openRegister', 'registers', 'movements', 'paymentMethods'));
    }

    /**
     * Abrir un nuevo Turno de Caja
     */
    public function open(Request $request)
    {
        $request->validate([
            'initial_amount' => 'required|numeric|min:0',
            'opening_notes' => 'nullable|string|max:500',
        ]);

        $existing = CashRegister::activeOpen();
        if ($existing) {
            Alert::warning('Caja ya abierta', 'Ya existe una caja abierta en esta sucursal. Ciérrela antes de abrir un nuevo turno.');
            return redirect()->route('cash_registers.index');
        }

        $company = Company::current();
        $branch = Branch::current();

        CashRegister::create([
            'company_id' => $company->id ?? null,
            'branch_id' => $branch->id ?? null,
            'user_id' => Auth::id(),
            'opening_date' => Carbon::now('America/La_Paz'),
            'initial_amount' => floatval($request->initial_amount),
            'expected_amount' => floatval($request->initial_amount),
            'status' => 'ABIERTA',
            'opening_notes' => $request->opening_notes,
        ]);

        Alert::toast('Turno de Caja abierto con éxito.', 'success');
        return redirect()->route('cash_registers.index');
    }

    /**
     * Registrar Ingreso o Egreso de Caja Chica / Gasto Operativo
     */
    public function storeMovement(Request $request)
    {
        $request->validate([
            'type' => 'required|in:INGRESO,EGRESO',
            'category' => 'required|string|max:60',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:400',
        ]);

        $openRegister = CashRegister::activeOpen();
        $branch = Branch::current();

        CashMovement::create([
            'cash_register_id' => $openRegister ? $openRegister->id : null,
            'branch_id' => $branch ? $branch->id : null,
            'user_id' => Auth::id(),
            'type' => $request->type,
            'category' => $request->category,
            'amount' => floatval($request->amount),
            'payment_method' => $request->payment_method ?? 'Efectivo (Bs.)',
            'reference' => $request->reference,
            'description' => $request->description,
            'movement_date' => Carbon::now('America/La_Paz'),
        ]);

        if ($openRegister) {
            $openRegister->recalculateTotals();
        }

        $label = $request->type === 'INGRESO' ? 'Ingreso de caja' : 'Gasto / Egreso operativo';
        Alert::toast("{$label} registrado correctamente.", 'success');
        return redirect()->route('cash_registers.index');
    }

    /**
     * Cerrar y Arquear Caja
     */
    public function close(Request $request, CashRegister $cashRegister)
    {
        $request->validate([
            'counted_amount' => 'required|numeric|min:0',
            'closing_notes' => 'nullable|string|max:500',
        ]);

        if ($cashRegister->status === 'CERRADA') {
            Alert::info('Caja Cerrada', 'Este turno de caja ya fue cerrado previamente.');
            return redirect()->route('cash_registers.index');
        }

        $cashRegister->recalculateTotals();
        $counted = floatval($request->counted_amount);
        $difference = round($counted - floatval($cashRegister->expected_amount), 2);

        $cashRegister->update([
            'closed_by_user_id' => Auth::id(),
            'closing_date' => Carbon::now('America/La_Paz'),
            'counted_amount' => $counted,
            'difference' => $difference,
            'status' => 'CERRADA',
            'closing_notes' => $request->closing_notes,
        ]);

        Alert::toast('Arqueo y Cierre de Caja realizado con éxito.', 'success');
        return redirect()->route('cash_registers.show', $cashRegister);
    }

    /**
     * Ver Detalle e Impresión de Arqueo de Caja
     */
    public function show(CashRegister $cashRegister)
    {
        if ($cashRegister->status === 'ABIERTA') {
            $cashRegister->recalculateTotals();
        }
        $cashRegister->load(['user', 'closedBy', 'branch', 'movements.user']);

        return view('admin.cash_register.show', compact('cashRegister'));
    }

    /**
     * Generar PDF del Reporte de Arqueo de Caja
     */
    public function pdf(CashRegister $cashRegister)
    {
        if ($cashRegister->status === 'ABIERTA') {
            $cashRegister->recalculateTotals();
        }
        $cashRegister->load(['user', 'closedBy', 'branch', 'movements.user']);

        $pdf = PDF::loadView('admin.cash_register.pdf', compact('cashRegister'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Arqueo_Caja_N_' . str_pad($cashRegister->id, 5, '0', STR_PAD_LEFT) . '.pdf');
    }
}

