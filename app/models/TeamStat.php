<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamStat extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'team_id',
        'matches',
        'won',
        'lost',
        'tied',
        'drawn',
        'win_percentage',
        'toss_won',
        'bat_first',
        'no_result',
    ];

    /**
     * Get the team that owns the stats.
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}

