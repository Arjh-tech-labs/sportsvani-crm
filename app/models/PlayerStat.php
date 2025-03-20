<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerStat extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'player_id',
        'matches',
        'runs',
        'wickets',
        'overs',
        'balls_faced',
        'average',
        'economy',
    ];

    /**
     * Get the player that owns the stats.
     */
    public function player()
    {
        return $this->belongsTo(User::class, 'player_id');
    }
}

