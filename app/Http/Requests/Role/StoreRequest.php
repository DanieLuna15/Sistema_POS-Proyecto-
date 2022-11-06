<?php

namespace App\Http\Requests\Role;

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
            'name'=>'required|string|min:5|max:40|unique:roles|regex:/^[\pL\s\-]+$/u',
            'slug'=>'required|string|min:5|max:40|unique:roles|regex:/^[\pL\s\-]+$/u',
            'description'=>'nullable|string|min:5|max:100',
        ];
    }
    public function messages()
    {
        return [
            'name.required'=>'Este campo es requerido',
            'name.string'=>'El valor no es correcto',
            'name.max'=>'Se necesitan como mínimo 5 caracteres',
            'name.max'=>'Solo se Permiten 40 caracteres',
            'name.unique'=>'Ya existe un Rol con el mismo nombre',
            'name.regex'=>'El Formato no es Válido (Elimine números y caracteres especiales)',

            'slug.required'=>'Este campo es requerido',
            'slug.string'=>'El valor no es correcto',
            'slug.max'=>'Se necesitan como mínimo 3 caracteres',
            'slug.max'=>'Solo se Permiten 40 caracteres',
            'slug.unique'=>'Ya existe un Rol con el mismo slug',
            'slug.regex'=>'El Formato del nombre no es Válido (Elimine números y caracteres especiales)',

            'description.min'=>'Se necesitan como mínimo 5 caracteres',
            'description.max'=>'Solo se permiten 100 caracteres',
            'description.string'=>'El valor no es correcto',
        ];
    }
}
