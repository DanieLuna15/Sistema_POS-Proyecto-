<?php

namespace App\Http\Requests\User;

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
            'name' => 'required|string|min:3|max:50|unique:users,name,' . $this->route('user')->id . '|regex:/^[\pL\s\-]+$/u',
            'email' => 'required|string|max:40|email:rfc|unique:users,email,' . $this->route('user')->id,
            'password' => 'nullable|string|min:8',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El valor del nombre no es correcto.',
            'name.min' => 'Se necesitan como mínimo 3 caracteres para el nombre.',
            'name.max' => 'Solo se permiten 50 caracteres para el nombre.',
            'name.unique' => 'Ya existe un usuario registrado con este mismo nombre.',
            'name.regex' => 'El formato del nombre no es válido (solo letras y espacios).',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.string' => 'El formato del correo electrónico no es válido.',
            'email.unique' => 'Esta dirección de correo electrónico ya se encuentra registrada.',
            'email.max' => 'Solo se permiten 40 caracteres para el correo.',
            'email.email' => 'No es un correo electrónico válido.',

            'password.min' => 'La contraseña debe contener como mínimo 8 caracteres.',
        ];
    }
}
