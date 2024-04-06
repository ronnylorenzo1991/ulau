<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'name'        => $this->name,
            'email'       => $this->email,
            'roles'       => $this->getRoleNames(),
            'permissions' => $this->getAllPermissions(),
            'navs'        => $this->navs,
            'center'      => $this->center,
        ];
    }
}
