<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Company;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        $companyId = Company::currentId();
        return [
            'name' => 'required|string|min:3|max:60',
            'ci' => ['required', 'string', 'min:4', 'max:20', Rule::unique('clients', 'ci')->where('company_id', $companyId)],
            'nit' => ['nullable', 'string', 'min:6', 'max:25', Rule::unique('clients', 'nit')->where('company_id', $companyId)],
            'address' => 'nullable|string|min:3|max:255',
            'phone' => ['required', 'string', 'min:6', 'max:20', Rule::unique('clients', 'phone')->where('company_id', $companyId)],
            'email' => ['nullable', 'string', 'max:60', 'email:rfc', Rule::unique('clients', 'email')->where('company_id', $companyId)],
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'El nombre del cliente es obligatorio.',
            'name.string' => 'El formato del nombre no es válido.',
            'name.min' => 'El nombre debe contener al menos 3 caracteres.',
            'name.max' => 'El nombre no debe exceder los 60 caracteres.',

            'ci.required' => 'La cédula de identidad (CI) es requerida.',
            'ci.unique' => 'Este número de CI ya se encuentra registrado.',
            'ci.min' => 'El CI debe contener al menos 4 caracteres.',
            'ci.max' => 'El CI no debe exceder los 20 caracteres.',

            'nit.unique' => 'Este NIT ya se encuentra registrado.',
            'nit.min' => 'El NIT debe contener al menos 6 caracteres.',
            'nit.max' => 'El NIT no debe exceder los 25 caracteres.',

            'address.max' => 'La dirección no debe exceder los 255 caracteres.',

            'phone.required' => 'El número de teléfono o celular es obligatorio.',
            'phone.unique' => 'Este número de teléfono ya se encuentra registrado.',
            'phone.min' => 'El teléfono debe contener al menos 6 dígitos.',
            'phone.max' => 'El teléfono no debe exceder los 20 dígitos.',

            'email.unique' => 'Esta dirección de correo ya se encuentra registrada.',
            'email.email' => 'El formato de correo electrónico no es válido.',
        ];
    }
}
