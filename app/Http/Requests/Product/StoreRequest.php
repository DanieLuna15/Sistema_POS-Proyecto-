<?php

namespace App\Http\Requests\Product;

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
            'name'=>'required|string|unique:products|max:50',
            //'image'=>'required|string|dimensions:min_width=100,min_height=200',
            //'image'=>['required'],
            'sell_price'=>'required',

            'category_id'=>'integer|required|exists:App\Category,id',
            'brand_id'=>'integer|required|exists:App\Brand,id',
            'provider_id'=>'integer|required|exists:App\Provider,id',
        ];
    }
    public function messages()
    {
        return [
            // 'name.required'=>'Este campo es requerido',
            // 'name.string'=>'El valor no es correcto',
            // 'name.max'=>'Solo se Permiten 50 caracteres',
            // 'name.unique'=>'El producto ya esta registrado',

            'name.required'=>'El campo nombre es requerido',
            'name.string'=>'El valor no es correcto en el campo nombre',
            'name.max'=>'Solo se Permiten 50 caracteres en el campo nombre',
            'name.unique'=>'Ya existe un producto con el mismo nombre',


            //'image.required'=>'La imagen es requerida',
            //'image.image'=>'Solo se permiten archivos con formato (.jpeg/.png/.bmp/.gif/.svg/.webp)',
            //'image.dimensions'=>'Solo se permiten imagenes mayores 100x200 px.',

            //'sell_price.required'=>'Este campo es requerido',
            'sell_price.required'=>'El campo precio de venta campo es requerido',

            'category_id.integer'=>'El valor tiene que ser entero.',
            'category_id.required'=>'Este campo es requerido',
            'category_id.exists'=>'La categoría no existe',

            'brand_id.integer'=>'El valor tiene que ser entero.',
            'brand_id.required'=>'Este campo es requerido',
            'brand_id.exists'=>'La marca no existe',

            'provider_id.integer'=>'El valor tiene que ser entero.',
            'provider_id.required'=>'Este campo es requerido',
            'provider_id.exists'=>'El Proveedor no existe',


        ];
    }
}
