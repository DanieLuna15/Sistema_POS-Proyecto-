<?php

namespace App\Exports;

use App\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Sale::with(['client', 'user'])
            ->whereRaw('year(sale_date) = year(now())')
            ->where('status', 'CONFIRMADO')
            ->orderBy('sale_date', 'DESC')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Venta',
            'Cliente',
            'Vendedor',
            'Fecha y Hora',
            'Total (Bs)',
            'Estado',
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->id,
            $sale->client ? $sale->client->name : 'Cliente General',
            $sale->user ? $sale->user->name : 'N/A',
            $sale->sale_date,
            $sale->total,
            $sale->status,
        ];
    }
}
