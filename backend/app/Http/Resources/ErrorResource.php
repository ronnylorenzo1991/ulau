<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ErrorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'message' => @$this->getMessage(),
            'code' => @$this->getCode(),
            'file' => @$this->getFile(),
            'line' => @$this->getLine(),
            'message_user' => 'Estimado Usuario ha ocurrido un error en el sistema'
        ];
    }
}
