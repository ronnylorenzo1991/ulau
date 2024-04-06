<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{
    protected $fillable = ['name', 'guard_name'];


    public function nav()
    {
        return $this->belongsToMany(Nav::class, 'role_nav');
    }
}
