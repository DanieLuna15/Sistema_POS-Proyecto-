<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Traits\BelongsToCompany;

class CashRegister extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'branch_id',
        'user_id',
        'closed_by_user_id',
        'opening_date',
        'closing_date',
        'initial_amount',
        'sales_cash',
        'sales_other',
        'credit_collections',
        'incomes_amount',
        'expenses_amount',
        'purchases_cash',
        'expected_amount',
        'counted_amount',
        'difference',
        'status',
        'opening_notes',
        'closing_notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function movements()
    {
        return $this->hasMany(CashMovement::class)->orderBy('movement_date', 'DESC');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Obtiene la caja abierta actualmente para la sucursal o usuario activo.
     */
    public static function activeOpen()
    {
        try {
            $branch = Branch::current();
            $query = self::where('status', 'ABIERTA');
            if ($branch && $branch->id) {
                $query->where(function ($q) use ($branch) {
                    $q->where('branch_id', $branch->id)->orWhereNull('branch_id');
                });
            }
            return $query->orderBy('opening_date', 'DESC')->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Recalcula los totales en vivo de una caja abierta.
     */
    public function recalculateTotals()
    {
        $sales = Sale::where('status', 'CONFIRMADO')
            ->where(function ($q) {
                $q->where('cash_register_id', $this->id)
                  ->orWhereBetween('sale_date', [$this->opening_date, $this->closing_date ?? now('America/La_Paz')]);
            })
            ->when($this->branch_id, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('branch_id', $this->branch_id)->orWhereNull('branch_id');
                });
            })
            ->get();

        $salesCash = 0;
        $salesOther = 0;

        foreach ($sales as $sale) {
            if ($sale->sale_type === 'CREDITO') {
                // Solo suma la cuota inicial en el momento de la venta
                $salesCash += floatval($sale->down_payment);
            } else {
                $methodUpper = strtoupper($sale->payment_method ?? '');
                if (str_contains($methodUpper, 'EFECTIVO') || str_contains($methodUpper, 'CONTADO')) {
                    $salesCash += floatval($sale->total);
                } else {
                    $salesOther += floatval($sale->total);
                }
            }
        }

        // Cobros de cuotas realizados durante el turno (filtrados por empresa actual vía relación sale)
        $creditCollections = SaleInstallment::whereHas('sale')
            ->where('status', 'PAGADO')
            ->whereBetween('payment_date', [$this->opening_date, $this->closing_date ?? now('America/La_Paz')])
            ->sum('paid_amount');

        $incomes = $this->movements()->where('type', 'INGRESO')->sum('amount');
        $expenses = $this->movements()->where('type', 'EGRESO')->sum('amount');

        $expectedCash = floatval($this->initial_amount) + $salesCash + floatval($creditCollections) + floatval($incomes) - floatval($expenses);

        $this->sales_cash = round($salesCash, 2);
        $this->sales_other = round($salesOther, 2);
        $this->credit_collections = round($creditCollections, 2);
        $this->incomes_amount = round($incomes, 2);
        $this->expenses_amount = round($expenses, 2);
        $this->expected_amount = round($expectedCash, 2);
        $this->save();

        return $this;
    }
}

