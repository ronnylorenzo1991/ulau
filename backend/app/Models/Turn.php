<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Turn extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'date_at',
        'time_at',
        'payment',
        'observations',
        'status_id',
        'client_id',
    ];

    public function status() {
        return $this->belongsTo(TurnStatus::class);
    }

    public function client() {
        return $this->belongsTo(User::class, 'client_id');
    }
}
