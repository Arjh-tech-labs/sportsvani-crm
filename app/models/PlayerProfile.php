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
        'player_type', // Batter, Bowler, Allrounder, WicketKeeper
        'batting_style', // Right, Left
        'bowling_style', // Various styles
        'highest_score',
        'best_bowling',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'stats' => 'array',
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
        return $this->hasOne(PlayerStat::class, 'player_id', 'user_id');
    }

    /**
     * Get the teams that the player is a member of.
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_players', 'player_id', 'team_id');
    }

    /**
     * Get the matches that the player has participated in.
     */
    public function matches()
    {
        return $this->belongsToMany(Match::class, 'match_players', 'player_id', 'match_id');
    }

    /**
     * Get the awards that the player has won.
     */
    public function awards()
    {
        return $this->hasMany(Award::class, 'player_id', 'user_id');
    }

    /**
     * Get the gallery images for the player.
     */
    public function gallery()
    {
        return $this->hasMany(Gallery::class, 'user_id', 'user_id')->where('type', 'player');
    }
}

