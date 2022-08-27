<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'=>'required|string|unique:products,name,'.$this->route('product')->id.'|max:50',
            'image'=>'required|string|dimensions:min_width=100,min_height=200',
            'sell_price'=>'required|',

            'category_id'=>'integer|required|exists:App\Category,id',
            'brand_id'=>'integer|required|exists:App\Brand,id',
            'provider_id'=>'integer|required|exists:App\Provider,id',
        ];
    }
    public function messages()
    {
        return [
            'name.required'=>'Éste campo es requerido',
            'name.string'=>'El valor no es correcto',
            'name.max'=>'Solo se Permiten 50 caracteres',
            'name.unique'=>'El producto ya esta registrado',

            'image.required'=>'La imagen es Requerida',
            'image.dimensions'=>'Solo se permiten imagenes de 100x200 px.',

            'sell_price.required'=>'Éste campo es requerido',

            'category_id.integer'=>'El valor tiene que ser entero.',
            'category_id.required'=>'Éste campo es requerido',
            'category_id.exists'=>'La categoría no existe',

            'brand_id.integer'=>'El valor tiene que ser entero.',
            'brand_id.required'=>'Éste campo es requerido',
            'brand_id.exists'=>'La marca no existe',

            'provider_id.integer'=>'El valor tiene que ser entero.',
            'provider_id.required'=>'Éste campo es requerido',
            'provider_id.exists'=>'El Proveedor no existe',


        ];
    }
}
