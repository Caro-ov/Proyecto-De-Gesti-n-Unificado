<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'date',
        'time',
        'location',
        'status',
        'capacity',
        'has_parking',
        'parking_slots',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
        'has_parking' => 'boolean',
    ];

    /**
     * El usuario que creó/organiza este evento
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}