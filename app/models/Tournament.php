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
        'tournament_id',
        'name',
        'logo',
        'banner',
        'organizer_id',
        'organizer_name',
        'organizer_mobile',
        'organizer_email',
        'start_date',
        'end_date',
        'category',
        'ball_type',
        'pitch_type',
        'match_type',
        'team_count',
        'fees',
        'winning_prize',
        'match_days',
        'match_timings',
        'format',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
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
        return $this->belongsToMany(Team::class, 'tournament_teams')
            ->withPivot('group_id', 'status', 'registered_at')
            ->withTimestamps();
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
     * Get the matches in the tournament.
     */
    public function matches()
    {
        return $this->hasMany(Match::class);
    }
}

