<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Http\Request;

use App\Http\Requests\Client\StoreRequest;
use App\Http\Requests\Client\UpdateRequest;

use Illuminate\Support\Facades\Auth;

//Para sweet alert en Clientes
use RealRashid\SweetAlert\Facades\Alert;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('can:clients.create')->only(['create','store']);
        $this->middleware('can:clients.index')->only(['index']);
        $this->middleware('can:clients.edit')->only(['edit','update']);
        $this->middleware('can:clients.show')->only(['show']);
    }

    public function index()
    {
        $clients = Client::get();
        return view('admin.client.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.client.create');
    }

    public function store(StoreRequest $request)
    {
        Client::create($request->all());
        if ($request->sale == 1) {
            Alert::toast('Cliente registrado con éxito.', 'success');
            return redirect()->back();
        }
        Alert::toast('Cliente registrado con éxito.', 'success');
        return redirect()->route('clients.index');
    }

    public function show(Client $client)
    {
        $total_purchases = 0;
        foreach ($client->sales as $key => $sale) {
            $total_purchases+=$sale->total;
        }
        return view('admin.client.show', compact('client', 'total_purchases'));
    }

    public function edit(Client $client)
    {
        return view('admin.client.edit', compact('client'));
    }

    public function update(UpdateRequest $request, Client $client)
    {
        $client->update($request->all());
        Alert::toast('Cliente actualizado con éxito.', 'success');
        return redirect()->route('clients.index');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index');
    }
}
