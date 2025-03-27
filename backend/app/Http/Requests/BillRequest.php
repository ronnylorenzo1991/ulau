<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BillRequest extends FormRequest
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
            'date_at'      => ['required'],
            'product_name' => ['required'],
            'payment'      => ['required', 'numeric'],
        ];
    }

    public function attributes()
    {
        return [
            'date_at'      => 'Fecha',
            'product_name' => 'Producto',
            'payment'      => 'Costo',
        ];
    }

    public function messages()
    {
        return [

        ];
    }
}
