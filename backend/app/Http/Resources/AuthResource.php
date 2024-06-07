<?php

namespace App\Http\Resources;

use App\Models\GeneralSetting;
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
            'phone'       => $this->phone,
            'roles'       => $this->getRoleNames(),
            'permissions' => $this->getAllPermissions(),
            'settings'    => GeneralSetting::all(),
        ];
    }
}
