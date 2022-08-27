<?php

namespace App\Http\Requests\Brand;

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
            'name'=>'required|string|max:40|unique:brands',
            'description'=>'nullable|string|max:100',
        ];
    }
    public function messages()
    {
        return [
            'name.required'=>'Éste campo es requerido',
            'name.string'=>'El valor no es correcto',
            'name.max'=>'Solo se Permiten 40 caracteres',
            'name.unique'=>'Ya existe una marca con el mismo nombre',

            'description.max'=>'Solo se permiten 100 caracteres',
            'description.string'=>'El valor no es correcto',
        ];
    }
}
