<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'player_type',
        'batting_style',
        'bowling_style',
        'highest_score',
        'best_bowling',
    ];

    /**
     * Get the user that owns the player profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the player's stats.
     */
    public function stats()
    {
        return $this->hasOneThrough(
            PlayerStat::class,
            User::class,
            'id', // Foreign key on users table
            'player_id', // Foreign key on player_stats table
            'user_id', // Local key on player_profiles table
            'id' // Local key on users table
        );
    }
}

