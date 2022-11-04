<?php

namespace App\Http\Controllers;

use App\Provider;
use Illuminate\Http\Request;

use App\Http\Requests\Provider\StoreRequest;
use App\Http\Requests\Provider\UpdateRequest;

use Illuminate\Support\Facades\Auth;

//Para sweet alert en Proveedores
use RealRashid\SweetAlert\Facades\Alert;
class ProviderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $providers = Provider::get();
        return view('admin.provider.index', compact('providers'));
    }

    public function create()
    {
        return view('admin.provider.create');
    }

    public function store(StoreRequest $request)
    {
        Provider::create($request->all());
        Alert::toast('Proveedor registrado con éxito.', 'success');
        return redirect()->route('providers.index');
    }

    public function show(Provider $provider)
    {
        return view('admin.provider.show', compact('provider'));
    }

    public function edit(Provider $provider)
    {
        return view('admin.provider.edit', compact('provider'));
    }

    public function update(UpdateRequest $request, Provider $provider)
    {
        $provider->update($request->all());
        Alert::toast('Proveedor actualizado con éxito.', 'success');
        return redirect()->route('providers.index');
    }

    public function destroy(Provider $provider)
    {
        $provider->delete();
        return redirect()->route('providers.index');
    }
}
