<?php

namespace App\Http\Requests\Role;

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
            'name'=>'required|string|unique:roles,name,'.$this->route('role')->id.'|min:5|max:40|regex:/^[\pL\s\-]+$/u',
            'slug'=>'required|string|unique:roles,slug,'.$this->route('role')->id.'|min:5|max:40|regex:/^[\pL\s\-]+$/u',
            'description'=>'nullable|string|min:5|max:100',
        ];
    }
    public function messages()
    {
        return [
            //'name.required'=>'Este campo es requerido',
            'name.required'=>'El campo nombre es requerido',
            //'name.string'=>'El valor no es correcto',
            'name.string'=>'El valor no es correcto en el campo nombre',
            //'name.max'=>'Se necesitan como mínimo 5 caracteres',
            'name.min'=>'Se necesitan como mínimo 5 caracteres en el campo nombre',
            //'name.max'=>'Solo se Permiten 40 caracteres',
            'name.max'=>'Solo se Permiten 40 caracteres en el campo nombre',
            //'name.unique'=>'Ya existe una Rol con el mismo nombre',
            'name.unique'=>'Ya existe un Rol con el mismo nombre',

            'name.regex'=>'El Formato del nombre no es Válido (Elimine números y caracteres especiales)',

            //'slug.required'=>'Este campo es requerido',
            'slug.required'=>'El campo Slug es requerido',
            //'slug.string'=>'El valor no es correcto',
            'slug.string'=>'El valor no es correcto en el campo nombre',
            //'slug.max'=>'Se necesitan como mínimo 3 caracteres',
            'slug.min'=>'Se necesitan como mínimo 5 caracteres en el campo nombre',
            //'slug.max'=>'Solo se Permiten 40 caracteres',
            'slug.max'=>'Solo se Permiten 40 caracteres en el campo nombre',
            //'slug.unique'=>'Ya existe una Rol con el mismo nombre',
            'slug.unique'=>'Ya existe un Rol con el mismo Slug',

            'slug.regex'=>'El Formato del nombre no es Válido (Elimine números y caracteres especiales)',


            //'description.min'=>'Se necesitan como mínimo 5 caracteres',
            'description.min'=>'Se necesitan como mínimo 5 caracteres en el campo descripcion',
            //'description.max'=>'Solo se permiten 100 caracteres',
            'description.max'=>'Solo se permiten 100 caracteres en el campo descripcion',
            //'description.string'=>'El valor no es correcto',
            'description.string'=>'El valor no es correcto en el campo descripcion',
        ];
    }
}
