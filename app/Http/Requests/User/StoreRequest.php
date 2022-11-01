<?php

namespace App\Http\Requests\User;

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
            'name'=>'required|string|min:3|max:50|unique:users|regex:/^[\pL\s\-]+$/u',
            'email'=>'required|string|max:40|unique:users|email:rfc,dns',
            'password'=>'required|string|min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
            //'role'=>'required',
            //'password'=>'required|string|min:8|confirmed',
        ];
    }
    public function messages()
    {
        return [
            //'name.required'=>'Este campo es requerido',
            'name.required'=>'El campo nombre es requerido',
            //'name.string'=>'El valor no es correcto',
            'name.string'=>'El valor no es correcto en el campo nombre',
            //'name.max'=>'Se necesitan como mínimo 3 caracteres',
            'name.min'=>'Se necesitan como mínimo 3 caracteres en el campo nombre',
            //'name.max'=>'Solo se Permiten 40 caracteres',
            'name.max'=>'Solo se Permiten 50 caracteres en el campo nombre',
            //'name.unique'=>'Ya existe una Categoría con el mismo nombre',
            'name.unique'=>'Ya existe un Usuario con el mismo nombre',
            'name.regex'=>'El Formato del nombre no es Válido (Elimine números y caracteres especiales)',

            // 'email.required'=>'Este campo es requerido',
            // 'email.string'=>'El valor no es correcto',
            // 'email.unique'=>'Esta direccion de correo electrónico ya se encuentra registrada',
            // 'email.max'=>'Solo se Permiten 40 caracteres',
            // 'email.email'=>'No es un correo electronico valido',
            'email.required'=>'El campo Correo es requerido',
            'email.string'=>'El valor no es correcto en el campo Correo',
            'email.unique'=>'Esta direccion de correo electrónico ya se encuentra registrada',
            'email.max'=>'Solo se Permiten 40 caracteres en el campo Correo',
            'email.email'=>'No es un correo electronico valido',

            'password.required'=>'La Contraseña es requerida',
            'password.string'=>'El valor no es correcto en el campo Contraseña',
            'password.min'=>'Se necesitan como mínimo 8 caracteres en el campo Contraseña',
            'password.regex'=>'La contraseña debe contener al menos un dígito (0-9), una minúscula(a-z), una mayúscula(A-Z) y al menos un caracter no alfanumérico(! - $ - # - %).',

            //'role.required'=>'Debes asignar un rol a este usuario',
        ];
    }
}
