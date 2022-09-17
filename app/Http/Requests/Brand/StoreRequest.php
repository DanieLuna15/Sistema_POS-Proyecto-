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
            'name'=>'required|string|min:3|max:40|unique:brands',
            'description'=>'nullable|string|max:100',
        ];
    }
    public function messages()
    {
        return [
            //'name.required'=>'Este campo es requerido',
            'name.required'=>'El campo nombre es requerido',
            //'name.string'=>'El valor no es correcto',
            'name.string'=>'El valor no es correcto en el campo nombre',
            //'name.max'=>'Se necesitan como mínimo 3 caracteres',
            'name.min'=>'Se necesitan como mínimo 3 caracteres en el campo nombre',
            //'name.max'=>'Solo se Permiten 40 caracteres',
            'name.max'=>'Solo se Permiten 40 caracteres en el campo nombre',
            //'name.unique'=>'Ya existe una marca con el mismo nombre',
            'name.unique'=>'Ya existe una marca con el mismo nombre',


            //'description.max'=>'Solo se permiten 100 caracteres',
            'description.max'=>'Solo se permiten 100 caracteres en el campo descripcion',
            //'description.string'=>'El valor no es correcto',
            'description.string'=>'El valor no es correcto en el campo descripcion',
        ];
    }
}
