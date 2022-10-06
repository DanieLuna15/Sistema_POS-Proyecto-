<?php

namespace App\Http\Controllers;

use App\Historydata;
use Illuminate\Http\Request;

class HistorydataController extends Controller
{
    public function index()
    {
        $historydatas = Historydata::get();
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Historydata $historydata)
    {
        //
    }

    public function edit(Historydata $historydata)
    {
        //
    }

    public function update(Request $request, Historydata $historydata)
    {
        //
    }

    public function destroy(Historydata $historydata)
    {
        //
    }
}
