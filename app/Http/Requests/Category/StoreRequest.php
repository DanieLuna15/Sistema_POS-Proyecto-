<?php

namespace App\Http\Requests\Category;

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
            'name'=>'required|string|min:3|max:40|unique:categories',
            'description'=>'nullable|string|min:5|max:100',
        ];
    }
    public function messages()
    {
        return [
            'name.required'=>'Este campo es requerido',
            'name.string'=>'El valor no es correcto',
            'name.min'=>'Se necesitan como mínimo 3 caracteres',
            'name.max'=>'Solo se Permiten 40 caracteres',
            'name.unique'=>'Ya existe una Categoría con el mismo nombre',

            'description.min'=>'Se necesitan como mínimo 5 caracteres',
            'description.max'=>'Solo se permiten 100 caracteres',
            'description.string'=>'El valor no es correcto',
        ];
    }
}
