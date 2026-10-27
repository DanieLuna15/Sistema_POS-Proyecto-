<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Company;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $companyId = Company::currentId();
        return [
            'name' => ['required', 'string', 'min:2', 'max:150', Rule::unique('products', 'name')->where('company_id', $companyId)],
            'code' => ['nullable', 'string', 'max:80', Rule::unique('products', 'code')->where('company_id', $companyId)],
            'sku' => 'nullable|string|max:80',
            'item_type' => 'nullable|in:PRODUCTO,SERVICIO,COMBO',
            'unit_measure' => 'nullable|string|max:30',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'purchase_price' => 'nullable|numeric|min:0',
            'sell_price' => 'required|numeric|min:0.01',
            'wholesale_price' => 'nullable|numeric|min:0',
            'wholesale_min_qty' => 'nullable|integer|min:1',
            'stock' => 'nullable|numeric|min:0',
            'stock_min' => 'nullable|integer|min:0',
            'location' => 'nullable|string|max:100',
            'batch_number' => 'nullable|string|max:80',
            'expiration_date' => 'nullable|date',
            'warranty_days' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'integer|required|exists:App\Category,id',
            'brand_id' => 'integer|required|exists:App\Brand,id',
            'provider_id' => 'integer|required|exists:App\Provider,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre del producto o servicio es requerido',
            'name.unique' => 'Este nombre de producto ya está registrado',
            'code.unique' => 'Este código de barras ya está asignado a otro producto',
            'sell_price.required' => 'El precio de venta es requerido',
            'sell_price.numeric' => 'El precio de venta debe ser numérico',
            'sell_price.min' => 'El precio de venta debe ser mayor a 0',
            'category_id.required' => 'Seleccione una categoría',
            'brand_id.required' => 'Seleccione una marca',
            'provider_id.required' => 'Seleccione un proveedor',
        ];
    }
}
