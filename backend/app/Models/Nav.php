<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class Nav extends Model
{
    use HasFactory;
    protected $fillable = [
        'label',
        'component',
        'name',
        'path',
        'parent',
        'icon',
        'code',
        'class',
        'route_name',
        'nav_id'
    ];
    public function navs()
    {
        return $this->hasMany(Nav::class, 'nav_id', 'id');
    }
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_nav');
    }
}
