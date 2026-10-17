<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'provider_id' => 'required|exists:providers,id',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric|min:1',
            'price' => 'required|array|min:1',
            'price.*' => 'required|numeric|min:0',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ];
    }

    public function messages()
    {
        return [
            'provider_id.required' => 'Debe seleccionar un proveedor para la compra.',
            'provider_id.exists' => 'El proveedor seleccionado no existe en el sistema.',
            'product_id.required' => 'Debe agregar al menos un producto a la compra.',
            'product_id.array' => 'Formato de productos no válido.',
            'product_id.min' => 'Debe incluir al menos un producto en el detalle.',
            'product_id.*.exists' => 'Uno de los productos seleccionados no existe.',
            'quantity.*.required' => 'La cantidad es requerida para cada producto.',
            'quantity.*.numeric' => 'La cantidad debe ser un valor numérico.',
            'quantity.*.min' => 'La cantidad mínima a ingresar es 1.',
            'price.*.required' => 'El precio de compra es requerido.',
            'price.*.numeric' => 'El precio debe ser un número válido.',
            'price.*.min' => 'El precio no puede ser negativo.',
            'picture.image' => 'El archivo adjunto debe ser una imagen.',
            'picture.mimes' => 'Formatos permitidos de comprobante: jpeg, png, jpg o webp.',
            'picture.max' => 'La imagen no debe pesar más de 3MB.',
        ];
    }
}
