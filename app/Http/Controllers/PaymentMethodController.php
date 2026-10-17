<?php

namespace App\Http\Controllers;

use App\PaymentMethod;
use App\Http\Requests\PaymentMethod\StoreRequest;
use App\Http\Requests\PaymentMethod\UpdateRequest;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class PaymentMethodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:payment_methods.index')->only(['index']);
        $this->middleware('can:payment_methods.create')->only(['create', 'store']);
        $this->middleware('can:payment_methods.edit')->only(['edit', 'update', 'change_status']);
        $this->middleware('can:payment_methods.destroy')->only(['destroy']);
    }

    public function index()
    {
        $paymentMethods = PaymentMethod::withCount('sales')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        $totalMethods = $paymentMethods->count();
        $totalContado = $paymentMethods->whereIn('payment_condition', ['CONTADO', 'AMBOS'])->count();
        $totalCredito = $paymentMethods->whereIn('payment_condition', ['CREDITO', 'AMBOS'])->count();
        $totalActivos = $paymentMethods->where('status', 'ACTIVO')->count();

        return view('admin.payment_method.index', compact(
            'paymentMethods',
            'totalMethods',
            'totalContado',
            'totalCredito',
            'totalActivos'
        ));
    }

    public function create()
    {
        return view('admin.payment_method.create');
    }

    public function store(StoreRequest $request)
    {
        $data = $request->all();
        $data['requires_reference'] = $request->has('requires_reference');

        if (empty($data['code'])) {
            $data['code'] = strtoupper(str_replace(' ', '_', preg_replace('/[^A-Za-z0-9 ]/', '', $data['name'])));
        }

        PaymentMethod::create($data);
        Alert::toast('Forma de pago registrada con éxito.', 'success');
        return redirect()->route('payment_methods.index');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('admin.payment_method.edit', compact('paymentMethod'));
    }

    public function update(UpdateRequest $request, PaymentMethod $paymentMethod)
    {
        $data = $request->all();
        $data['requires_reference'] = $request->has('requires_reference');

        $paymentMethod->update($data);
        Alert::toast('Forma de pago actualizada con éxito.', 'success');
        return redirect()->route('payment_methods.index');
    }

    public function change_status(PaymentMethod $paymentMethod)
    {
        $newStatus = ($paymentMethod->status === 'ACTIVO') ? 'INACTIVO' : 'ACTIVO';
        $paymentMethod->update(['status' => $newStatus]);

        $msg = ($newStatus === 'ACTIVO') 
            ? 'Forma de pago activada con éxito.' 
            : 'Forma de pago desactivada con éxito.';

        Alert::toast($msg, 'success');
        return redirect()->back();
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->sales()->count() > 0) {
            Alert::error('Operación no permitida', 'Esta forma de pago ya tiene ventas asociadas. Para restringir su uso, cambie su estado a Inactivo.');
            return redirect()->back();
        }

        $paymentMethod->delete();
        Alert::toast('Forma de pago eliminada con éxito.', 'success');
        return redirect()->route('payment_methods.index');
    }
}
