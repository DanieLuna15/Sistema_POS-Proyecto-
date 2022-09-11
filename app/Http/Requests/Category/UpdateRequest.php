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
            'name'=>'required|string|unique:categories,name,'.$this->route('category')->id.'|max:40',
            'description'=>'nullable|string|max:100',
        ];
    }
    public function messages()
    {
        return [
            'name.required'=>'Este campo es requerido',
            'name.string'=>'El valor no es correcto',
            'name.max'=>'Solo se Permiten 40 caracteres',
            'name.unique'=>'Ya existe una categoría con el mismo nombre',

            'description.max'=>'Solo se permiten 100 caracteres',
            'description.string'=>'El valor no es correcto',
        ];
    }
}
