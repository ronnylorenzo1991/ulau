<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'value', 'active'];

    protected $casts = [
        'value' => 'array'
    ];
}
