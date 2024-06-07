<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anomaly extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_label',
        'coordinates',
        'event_id',
    ];

    public function event()
    {
        return $this->belongsToMany(Event::class);
    }
}
