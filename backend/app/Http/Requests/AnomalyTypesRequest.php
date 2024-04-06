<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnomalyTypesRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'min:5', 'max:190', 'unique:anomaly_types,name,' . request()->id],
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nombre',
        ];
    }

    public function messages()
    {
        return [
            'name.min'      => 'El :attribute tiene que tener al menos 5 caracteres.',
            'name.max'      => 'El :attribute es demasiado largo.',
            'name.required' => 'El :attribute es requerido.',
            'name.unique'   => 'Ya existe un registro con el mismo :attribute.',
        ];
    }
}
