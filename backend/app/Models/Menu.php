<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    public $table = "menus";
    protected $fillable = ['path', 'component', 'orden'];

    public function subMenus()
    {
        return $this->hasMany(Menu::class, 'menus_id')->orderBy('orden');
    }
}
