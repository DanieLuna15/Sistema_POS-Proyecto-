<?php

namespace App\Http\Requests\Provider;

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
            'name'=>'required|string|unique:providers,name,'.$this->route('provider')->id.'|max:40',
            'email'=>'required|string|unique:providers,email,'.$this->route('provider')->id.'|max:40|email:rfc,dns',
            'nit'=>'required|string|min:11|unique:providers,nit,'.$this->route('provider')->id.'|max:11',
            'address'=>'nullable|string|max:200',
            'phone'=>'required|string|min:8|unique:providers,phone,'.$this->route('provider')->id.'|max:8',
        ];
    }
    public function messages()
    {
        return [
            // 'name.required'=>'Este campo es requerido',
            // 'name.string'=>'El valor no es correcto',
            // 'name.max'=>'Solo se Permiten 40 caracteres',
            // 'name.unique'=>'Ya existe un proveedor con el mismo nombre',
            'name.required'=>'El campo nombre es requerido',
            'name.string'=>'El valor no es correcto en el campo nombre',
            'name.max'=>'Solo se Permiten 40 caracteres en el campo nombre',
            'name.unique'=>'Ya existe un proveedor con el mismo nombre',

            // 'email.required'=>'Este campo es requerido',
            // 'email.email'=>'No es un correo electrónico',
            // 'email.string'=>'El valor no es correcto',
            // 'email.max'=>'Solo se Permiten 40 caracteres',
            // 'email.unique'=>'Ya existe un proveedor con el mismo correo',
            'email.required'=>'El campo correo es requerido',
            'email.email'=>'No es un correo electrónico',
            'email.string'=>'El valor no es correcto en el campo correo',
            'email.max'=>'Solo se Permiten 40 caracteres en el campo correo',
            'email.unique'=>'Ya existe un proveedor con el mismo correo',

            // 'nit.required'=>'Este campo es requerido',
            // 'nit.string'=>'El valor no es correcto',
            // 'nit.max'=>'Solo se Permiten 11 caracteres',
            // 'nit.min'=>'Se requiere de 11 caracteres',
            // 'nit.unique'=>'Ya existe un proveedor con el mismo nit',
            'nit.required'=>'El campo NIT es requerido',
            'nit.string'=>'El valor no es correcto en el campo NIT',
            'nit.max'=>'Solo se Permiten 11 caracteres en el campo NIT',
            'nit.min'=>'Se requiere de 11 caracteres en el campo NIT',
            'nit.unique'=>'Ya existe un proveedor con el mismo NIT',

            // 'address.max'=>'Solo se permiten 200 caracteres',
            // 'address.string'=>'El valor no es correcto',
            'address.max'=>'Solo se permiten 200 caracteres en el campo Dirección',
            'address.string'=>'El valor no es correcto en el campo Dirección',

            // 'phone.required'=>'Este campo es requerido',
            // 'phone.string'=>'El valor no es correcto',
            // 'phone.max'=>'Solo se Permiten 8 caracteres',
            // 'phone.min'=>'Se requiere de 8 caracteres',
            // 'phone.unique'=>'Ya existe un proveedor con el mismo teléfono',
            'phone.required'=>'El campo Teléfono/celular es requerido',
            'phone.string'=>'El valor no es correcto en el campo Teléfono/celular',
            'phone.max'=>'Solo se Permiten 8 caracteres en el campo Teléfono/celular',
            'phone.min'=>'Se requiere de 8 caracteres en el campo Teléfono/celular',
            'phone.unique'=>'Ya existe un proveedor con el mismo Teléfono/Celular',
        ];
    }
}
