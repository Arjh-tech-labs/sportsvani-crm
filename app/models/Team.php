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
        'team_id', // Unique team ID
        'name',
        'logo',
        'captain_id', // User ID of captain
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
        return $this->belongsToMany(User::class, 'team_players', 'team_id', 'player_id');
    }

    /**
     * Get the managers of the team.
     */
    public function managers()
    {
        return $this->belongsToMany(User::class, 'team_managers', 'team_id', 'manager_id');
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
        return $this->belongsToMany(Tournament::class, 'tournament_teams');
    }

    /**
     * Get the matches that the team has played.
     */
    public function matches()
    {
        return $this->belongsToMany(Match::class, 'match_teams');
    }

    /**
     * Get the awards that the team has won.
     */
    public function awards()
    {
        return $this->hasMany(Award::class, 'team_id');
    }

    /**
     * Get the gallery images for the team.
     */
    public function gallery()
    {
        return $this->hasMany(Gallery::class, 'team_id')->where('type', 'team');
    }
}

