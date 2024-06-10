<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TurnRequest extends FormRequest
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
            'date_at'   => ['required'],
            'time_at'   => ['required',Rule::unique('turns')->where(fn(Builder $query)   => $query->where('date_at', request()->get('date_at')))],
            'payment'   => ['numeric'],
            'client_id' => ['required', 'gte:1'],
        ];
    }

    public function attributes()
    {
        return [
            'date_at'   => 'Fecha',
            'time_at'   => 'Hora',
            'payment'   => 'Pago',
            'client_id' => 'Clienta',
        ];
    }

    public function messages()
    {
        return [
            'time_at.unique'     => 'Este turno ya está ocupado',
            'client_id.gte'     => 'Debe Seleccionar una :attribute',
        ];
    }
}
