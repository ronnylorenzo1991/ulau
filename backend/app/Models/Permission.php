<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    public function navs()
    {
        return $this->belongsToMany(Nav::class);
    }
}
