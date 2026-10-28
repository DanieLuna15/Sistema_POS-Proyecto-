<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if (!$this->filled('client_id')) {
            $defaultClient = \App\Client::ensureDefaultForCompany();
            if ($defaultClient) {
                $this->merge(['client_id' => $defaultClient->id]);
            }
        }
    }

    public function rules()
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric|min:1',
            'price' => 'required|array|min:1',
            'price.*' => 'required|numeric|min:0',
            'discount' => 'nullable|array',
            'discount.*' => 'nullable|numeric|min:0|max:100',
            'sale_type' => 'nullable|string|in:REGULAR,POS_RAPIDO,CREDITO',
            'payment_method' => 'nullable|string',
            'cash_received' => 'nullable|numeric|min:0',
            'down_payment' => 'nullable|numeric|min:0',
            'installments_count' => 'nullable|integer|min:1|max:60',
            'installment_frequency' => 'nullable|string|in:SEMANAL,QUINCENAL,MENSUAL',
        ];
    }

    public function messages()
    {
        return [
            'client_id.required' => 'Debe seleccionar un cliente para la venta.',
            'client_id.exists' => 'El cliente seleccionado no es válido.',
            'product_id.required' => 'Debe agregar al menos un producto a la venta.',
            'product_id.array' => 'Formato de productos no válido.',
            'product_id.min' => 'Debe incluir al menos un producto en el detalle.',
            'product_id.*.exists' => 'Uno de los productos seleccionados no existe en el sistema.',
            'quantity.*.required' => 'La cantidad es obligatoria para cada producto.',
            'quantity.*.numeric' => 'La cantidad debe ser un valor numérico.',
            'quantity.*.min' => 'La cantidad mínima a vender es 1.',
            'price.*.required' => 'El precio es obligatorio para cada producto.',
            'price.*.numeric' => 'El precio debe ser un número válido.',
            'price.*.min' => 'El precio no puede ser negativo.',
            'discount.*.numeric' => 'El descuento debe ser un valor numérico.',
            'discount.*.min' => 'El descuento no puede ser negativo.',
            'discount.*.max' => 'El descuento no puede superar el 100%.',
        ];
    }
}
