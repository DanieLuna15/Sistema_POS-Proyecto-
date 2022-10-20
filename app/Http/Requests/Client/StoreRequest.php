<?php

namespace App\Http\Requests\Client;

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
            'name'=>'required|string|max:50',
            'ci'=>'required|string|unique:clients|max:7|min:7',
            'nit'=>'nullable|string|unique:clients|max:11|min:11',

            'address'=>'nullable|string|max:255',
            'phone'=>'required|string|nullable|unique:clients|max:8|min:8',
            'email'=>'nullable|string|max:40|unique:clients|email:rfc,dns',
        ];
    }
    public function messages()
    {
        return [
            // 'name.required'=>'Este campo es requerido',
            // 'name.string'=>'El valor no es correcto',
            // 'name.max'=>'Solo se Permiten 50 caracteres',
            'name.required'=>'El campo Nombre es requerido',
            'name.string'=>'El valor no es correcto en el Campo Nombre',
            'name.max'=>'Solo se Permiten 50 caracteres en el campo Nombre',

            // 'ci.required'=>'Este campo es requerido',
            // 'ci.string'=>'El valor no es correcto',
            // 'ci.unique'=>'Este CI ya se encuentra registrado',
            // 'ci.min'=>'Se requiere de 7 caracteres',
            // 'ci.max'=>'Solo se Permiten 7 caracteres',
            'ci.required'=>'El campo CI es requerido',
            'ci.string'=>'El valor no es correcto en el Campo CI',
            'ci.unique'=>'Este CI ya se encuentra registrado',
            'ci.min'=>'Se requiere de 7 caracteres en el campo CI',
            'ci.max'=>'Solo se Permiten 7 caracteres en el campo CI',

            // 'nit.string'=>'El valor no es correcto',
            // 'nit.unique'=>'Este NIT ya se encuentra registrado',
            // 'nit.min'=>'Se requiere de 11 caracteres',
            // 'nit.max'=>'Solo se Permiten 11 caracteres',
            'nit.string'=>'El valor no es correcto en el Campo NIT',
            'nit.unique'=>'Este NIT ya se encuentra registrado',
            'nit.min'=>'Se requiere de 11 caracteres en el Campo NIT',
            'nit.max'=>'Solo se Permiten 11 caracteres en el Campo NIT',

            // 'address.string'=>'El valor no es correcto',
            // 'address.max'=>'Solo se Permiten 255 caracteres',
            'address.string'=>'El valor no es correcto en el campo Dirección',
            'address.max'=>'Solo se Permiten 255 caracteres en el campo Dirección',

            // 'phone.required'=>'Este campo es requerido',
            // 'phone.string'=>'El valor no es correcto',
            // 'phone.unique'=>'Este numero de celular ya se encuentra registrado',
            // 'phone.min'=>'Se requiere de 8 caracteres',
            // 'phone.max'=>'Solo se Permiten 8 caracteres',
            'phone.required'=>'El campo Telefono/Celular es requerido',
            'phone.string'=>'El valor no es correcto en el campo Telefono/Celular',
            'phone.unique'=>'Este numero de celular ya se encuentra registrado',
            'phone.min'=>'Se requiere de 8 caracteres en el campo Telefono/Celular',
            'phone.max'=>'Solo se Permiten 8 caracteres en el campo Telefono/Celular',

            // 'email.string'=>'El valor no es correcto',
            // 'email.unique'=>'Esta direccion de correo electrónico ya se encuentra registrada',
            // 'email.max'=>'Solo se Permiten 40 caracteres',
            // 'email.email'=>'No es un correo electronico valido',
            'email.string'=>'El valor no es correcto en el campo Correo',
            'email.unique'=>'Esta direccion de correo electrónico ya se encuentra registrada',
            'email.max'=>'Solo se Permiten 40 caracteres en el campo Correo',
            'email.email'=>'No es un correo electronico valido',

        ];
    }
}
