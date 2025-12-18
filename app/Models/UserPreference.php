<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'temperature_unit',
        'wind_speed_unit',
        'auto_location',
        'default_city',
        'default_latitude',
        'default_longitude',
    ];

    protected function casts(): array
    {
        return [
            'auto_location' => 'boolean',
            'default_latitude' => 'decimal:7',
            'default_longitude' => 'decimal:7',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
