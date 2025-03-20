<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'match_id',
        'name',
        'match_type',
        'ball_type',
        'pitch_type',
        'overs',
        'powerplay_overs',
        'overs_per_bowler',
        'city',
        'ground',
        'date',
        'team_a_id',
        'team_b_id',
        'toss_winner_id',
        'toss_decision',
        'tournament_id',
        'round_id',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * Generate a unique match ID
     */
    public static function generateUniqueMatchId()
    {
        $prefix = 'MTH';
        $randomPart = mt_rand(1000, 9999);
        $uniqueId = $prefix . $randomPart;
        
        // Check if ID already exists
        while (self::where('match_id', $uniqueId)->exists()) {
            $randomPart = mt_rand(1000, 9999);
            $uniqueId = $prefix . $randomPart;
        }
        
        return $uniqueId;
    }

    /**
     * Get team A.
     */
    public function teamA()
    {
        return $this->belongsTo(Team::class, 'team_a_id');
    }

    /**
     * Get team B.
     */
    public function teamB()
    {
        return $this->belongsTo(Team::class, 'team_b_id');
    }

    /**
     * Get the toss winner.
     */
    public function tossWinner()
    {
        return $this->belongsTo(Team::class, 'toss_winner_id');
    }

    /**
     * Get the tournament that the match is part of.
     */
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * Get the round that the match is part of.
     */
    public function round()
    {
        return $this->belongsTo(TournamentRound::class, 'round_id');
    }

    /**
     * Get the officials for the match.
     */
    public function officials()
    {
        return $this->belongsToMany(User::class, 'match_officials')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the players for the match.
     */
    public function players()
    {
        return $this->belongsToMany(User::class, 'match_players', 'match_id', 'player_id')
            ->withPivot('team_id', 'role')
            ->withTimestamps();
    }

    /**
     * Get the result for the match.
     */
    public function result()
    {
        return $this->hasOne(MatchResult::class);
    }

    /**
     * Get the innings for the match.
     */
    public function innings()
    {
        return $this->hasMany(Innings::class);
    }
}

