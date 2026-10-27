<?php

namespace App\Http\Requests\Provider;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Company;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $companyId = Company::currentId();
        $providerId = $this->route('provider')->id;
        return [
            'name' => ['required', 'string', 'min:3', 'max:60', Rule::unique('providers', 'name')->where('company_id', $companyId)->ignore($providerId)],
            'email' => ['required', 'string', 'max:60', 'email:rfc', Rule::unique('providers', 'email')->where('company_id', $companyId)->ignore($providerId)],
            'nit' => ['required', 'string', 'min:6', 'max:25', Rule::unique('providers', 'nit')->where('company_id', $companyId)->ignore($providerId)],
            'address' => 'nullable|string|min:3|max:200',
            'phone' => ['required', 'string', 'min:6', 'max:20', Rule::unique('providers', 'phone')->where('company_id', $companyId)->ignore($providerId)],
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
