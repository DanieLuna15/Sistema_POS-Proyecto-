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
            'name' => 'required|string|min:3|max:60|unique:providers,name,' . $this->route('provider')->id,
            'email' => 'required|string|max:60|email:rfc|unique:providers,email,' . $this->route('provider')->id,
            'nit' => 'required|string|min:6|max:25|unique:providers,nit,' . $this->route('provider')->id,
            'address' => 'nullable|string|min:3|max:200',
            'phone' => 'required|string|min:6|max:20|unique:providers,phone,' . $this->route('provider')->id,
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'El nombre del proveedor es obligatorio.',
            'name.string' => 'El formato del nombre no es correcto.',
            'name.min' => 'Se necesitan como mínimo 3 caracteres para el nombre.',
            'name.max' => 'Solo se permiten 60 caracteres para el nombre.',
            'name.unique' => 'Ya existe un proveedor registrado con el mismo nombre.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.unique' => 'Ya existe un proveedor con este mismo correo.',

            'nit.required' => 'El NIT o número de identificación fiscal es obligatorio.',
            'nit.min' => 'El NIT debe tener al menos 6 caracteres.',
            'nit.max' => 'El NIT no debe superar los 25 caracteres.',
            'nit.unique' => 'Ya existe un proveedor registrado con este mismo NIT.',

            'address.max' => 'La dirección no debe superar los 200 caracteres.',

            'phone.required' => 'El teléfono de contacto es obligatorio.',
            'phone.min' => 'El teléfono debe contener al menos 6 dígitos.',
            'phone.max' => 'El teléfono no debe superar los 20 dígitos.',
            'phone.unique' => 'Ya existe un proveedor registrado con el mismo teléfono.',
        ];
    }
}
