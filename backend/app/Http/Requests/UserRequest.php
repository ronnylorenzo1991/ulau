<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'  => ['required', 'max:190'],
            'phone' => ['required', 'min:8', 'unique:users,phone,' . request()->id],
        ];
    }

    public function attributes()
    {
        return [
            'name'  => 'Nombre',
            'phone' => 'Teléfono',
        ];
    }

    public function messages()
    {
        return [
            'phone.min'     => 'El :attribute tiene que tener al menos 8 números.',
            'phone.unique'  => 'Ya existe un registro con el mismo :attribute.',
            'name.max'      => 'El :attribute es demasiado largo.',
            'name.required' => 'El :attribute es requerido.',
        ];
    }
}
