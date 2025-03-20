<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'team_id',
        'name',
        'logo',
        'captain_id',
        'captain_name',
        'captain_mobile',
    ];

    /**
     * Generate a unique team ID
     */
    public static function generateUniqueTeamId()
    {
        $prefix = 'TM';
        $randomPart = mt_rand(1000, 9999);
        $uniqueId = $prefix . $randomPart;
        
        // Check if ID already exists
        while (self::where('team_id', $uniqueId)->exists()) {
            $randomPart = mt_rand(1000, 9999);
            $uniqueId = $prefix . $randomPart;
        }
        
        return $uniqueId;
    }

    /**
     * Get the captain of the team.
     */
    public function captain()
    {
        return $this->belongsTo(User::class, 'captain_id');
    }

    /**
     * Get the players in the team.
     */
    public function players()
    {
        return $this->belongsToMany(User::class, 'team_players', 'team_id', 'player_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
       'player_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the team's stats.
     */
    public function stats()
    {
        return $this->hasOne(TeamStat::class);
    }

    /**
     * Get the tournaments that the team has participated in.
     */
    public function tournaments()
    {
        return $this->belongsToMany(Tournament::class, 'tournament_teams')
            ->withPivot('group_id', 'status', 'registered_at')
            ->withTimestamps();
    }

    /**
     * Get the matches that the team has played as team A.
     */
    public function homeMatches()
    {
        return $this->hasMany(Match::class, 'team_a_id');
    }

    /**
     * Get the matches that the team has played as team B.
     */
    public function awayMatches()
    {
        return $this->hasMany(Match::class, 'team_b_id');
    }

    /**
     * Get all matches for the team.
     */
    public function matches()
    {
        return Match::where('team_a_id', $this->id)
            ->orWhere('team_b_id', $this->id);
    }
}

