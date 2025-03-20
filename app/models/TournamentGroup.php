<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentGroup extends Model
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
    ];

    /**
     * Get the tournament that owns the group.
     */
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * Get the teams in the group.
     */
    public function teams()
    {
        return $this->hasManyThrough(
            Team::class,
            TournamentTeam::class,
            'group_id', // Foreign key on tournament_teams table
            'id', // Foreign key on teams table
            'id', // Local key on tournament_groups table
            'team_id' // Local key on tournament_teams table
        );
    }
}

