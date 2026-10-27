<?php

namespace App\Http\Requests\PaymentMethod;

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
        $id = $this->route('payment_method')->id ?? $this->route('payment_method');
        $companyId = Company::currentId();

        return [
            'name' => ['required', 'string', 'min:2', 'max:100', Rule::unique('payment_methods', 'name')->where('company_id', $companyId)->ignore($id)],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('payment_methods', 'code')->where('company_id', $companyId)->ignore($id)],
            'payment_condition' => 'required|in:CONTADO,CREDITO,AMBOS',
            'description' => 'nullable|string|max:500',
            'instructions' => 'nullable|string|max:500',
            'requires_reference' => 'nullable|boolean',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'status' => 'required|in:ACTIVO,INACTIVO',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre de la forma de pago es obligatorio.',
            'name.unique' => 'Ya existe una forma de pago con este nombre.',
            'payment_condition.required' => 'Debe seleccionar si aplica a Contado, Crédito o Ambos.',
            'payment_condition.in' => 'La condición seleccionada no es válida.',
        ];
    }
}
