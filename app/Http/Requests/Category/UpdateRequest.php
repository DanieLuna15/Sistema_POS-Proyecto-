<?php

namespace App\Http\Requests\Category;

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
            'nombre'=>'required|string|max:40',
            'descripcion'=>'nullable|string|max:100',
        ];
    }
    public function messages()
    {
        return [
            'nombre.required'=>'Éste campo es requerido',
            'nombre.string'=>'El valor no es correcto',
            'nombre.max'=>'Solo se Permiten 40 caracteres',
            'descripcion.required'=>'Éste campo es requerido',
            'descripcion.string'=>'El valor no es correcto',
        ];
    }
}
