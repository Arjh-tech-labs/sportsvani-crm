<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tournament_id', // Unique tournament ID
        'name',
        'logo',
        'banner',
        'organizer_id', // User ID of organizer
        'organizer_name',
        'organizer_mobile',
        'organizer_email',
        'start_date',
        'end_date',
        'category', // Open, Corporate, Community, School, College, University Series, Other
        'ball_type', // Leather, Tennis, Other
        'pitch_type', // Rough, Cement, Turf, Matt, Other
        'match_type', // Limited Overs, Box/Turf, Test Match
        'team_count',
        'fees',
        'winning_prize', // Cash, Trophy, Both
        'match_days', // JSON array of days
        'match_timings', // Day, Night, Day & Night
        'format', // League, Knockout
        'status', // Upcoming, Active, Completed, Cancelled
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'match_days' => 'array',
    ];

    /**
     * Generate a unique tournament ID
     */
    public static function generateUniqueTournamentId()
    {
        $prefix = 'TRN';
        $randomPart = mt_rand(1000, 9999);
        $uniqueId = $prefix . $randomPart;
        
        // Check if ID already exists
        while (self::where('tournament_id', $uniqueId)->exists()) {
            $randomPart = mt_rand(1000, 9999);
            $uniqueId = $prefix . $randomPart;
        }
        
        return $uniqueId;
    }

    /**
     * Get the organizer of the tournament.
     */
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * Get the teams in the tournament.
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'tournament_teams');
    }

    /**
     * Get the groups in the tournament.
     */
    public function groups()
    {
        return $this->hasMany(TournamentGroup::class);
    }

    /**
     * Get the rounds in the tournament.
     */
    public function rounds()
    {
        return $this->hasMany(TournamentRound::class);
    }

    /**
     * Get the officials in the tournament.
     */
    public function officials()
    {
        return $this->belongsToMany(User::class, 'tournament_officials')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the matches in the tournament.
     */
    public function matches()
    {
        return $this->hasMany(Match::class);
    }

    /**
     * Get the points table for the tournament.
     */
    public function pointsTable()
    {
        return $this->hasOne(TournamentPointsTable::class);
    }

    /**
     * Get the leaderboard for the tournament.
     */
    public function leaderboard()
    {
        return $this->hasOne(TournamentLeaderboard::class);
    }

    /**
     * Get the gallery images for the tournament.
     */
    public function gallery()
    {
        return $this->hasMany(Gallery::class, 'tournament_id')->where('type', 'tournament');
    }
}

