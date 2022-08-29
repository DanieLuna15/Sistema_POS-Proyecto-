<?php

namespace App\Http\Requests\Provider;

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
            'name'=>'required|string|max:40|unique:providers',
            'email'=>'required|string|max:40|unique:providers|email:rfc,dns',
            'nit'=>'required|string|max:11|min:11|unique:providers',
            'address'=>'nullable|string|max:200',
            'phone'=>'required|string|max:8|min:8|unique:providers',
        ];
    }
    public function messages()
    {
        return [
            'name.required'=>'Éste campo es requerido',
            'name.string'=>'El valor no es correcto',
            'name.max'=>'Solo se Permiten 40 caracteres',
            'name.unique'=>'Ya existe un proveedor con el mismo nombre',

            'email.required'=>'Éste campo es requerido',
            'email.email'=>'No es un correo electrónico',
            'email.string'=>'El valor no es correcto',
            'email.max'=>'Solo se Permiten 40 caracteres',
            'email.unique'=>'Ya existe un proveedor con el mismo correo',

            'nit.required'=>'Éste campo es requerido',
            'nit.string'=>'El valor no es correcto',
            'nit.max'=>'Solo se Permiten 11 caracteres',
            'nit.min'=>'Se requiere de 11 caracteres',
            'nit.unique'=>'Ya existe un proveedor con el mismo nit',

            'address.max'=>'Solo se permiten 200 caracteres',
            'address.string'=>'El valor no es correcto',

            'phone.required'=>'Éste campo es requerido',
            'phone.string'=>'El valor no es correcto',
            'phone.max'=>'Solo se Permiten 8 caracteres',
            'phone.min'=>'Se requiere de 8 caracteres',
            'phone.unique'=>'Ya existe un proveedor con el mismo teléfono',
        ];
    }
}
