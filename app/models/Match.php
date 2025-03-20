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
        'match_id', // Unique match ID
        'name',
        'match_type', // Limited Overs, Test
        'ball_type', // Leather, Tennis, Other
        'pitch_type', // Rough, Matt, Cement, Turf, Other
        'overs',
        'powerplay_overs',
        'overs_per_bowler',
        'city',
        'ground',
        'date',
        'team_a_id',
        'team_b_id',
        'toss_winner_id',
        'toss_decision', // Bat, Bowl
        'tournament_id',
        'round_id',
        'status', // Scheduled, Live, Completed, Abandoned, Cancelled
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'datetime',
        'officials' => 'array',
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
        return $this->belongsToMany(User::class, 'match_players')
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
     * Get the batting scorecard for the match.
     */
    public function battingScorecard()
    {
        return $this->hasMany(BattingScorecard::class);
    }

    /**
     * Get the bowling scorecard for the match.
     */
    public function bowlingScorecard()
    {
        return $this->hasMany(BowlingScorecard::class);
    }

    /**
     * Get the events for the match.
     */
    public function events()
    {
        return $this->hasMany(MatchEvent::class);
    }

    /**
     * Get the wagon wheel data for the match.
     */
    public function wagonWheel()
    {
        return $this->hasMany(WagonWheel::class);
    }

    /**
     * Get the live stream for the match.
     */
    public function liveStream()
    {
        return $this->hasOne(LiveStream::class);
    }

    /**
     * Get the gallery images for the match.
     */
    public function gallery()
    {
        return $this->hasMany(Gallery::class, 'match_id')->where('type', 'match');
    }
}

