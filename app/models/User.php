<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'city',
        'location',
        'user_id', // Unique user ID
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Generate a unique user ID
     */
    public static function generateUniqueUserId()
    {
        $prefix = 'USR';
        $randomPart = mt_rand(10000, 99999);
        $uniqueId = $prefix . $randomPart;
        
        // Check if ID already exists
        while (self::where('user_id', $uniqueId)->exists()) {
            $randomPart = mt_rand(10000, 99999);
            $uniqueId = $prefix . $randomPart;
        }
        
        return $uniqueId;
    }

    /**
     * Get the roles for the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Get the player profile for the user.
     */
    public function playerProfile()
    {
        return $this->hasOne(PlayerProfile::class);
    }

    /**
     * Get the umpire profile for the user.
     */
    public function umpireProfile()
    {
        return $this->hasOne(UmpireProfile::class);
    }

    /**
     * Get the scorer profile for the user.
     */
    public function scorerProfile()
    {
        return $this->hasOne(ScorerProfile::class);
    }

    /**
     * Get the teams that the user is a member of.
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_players');
    }

    /**
     * Get the teams that the user manages.
     */
    public function managedTeams()
    {
        return $this->hasMany(Team::class, 'captain_id');
    }

    /**
     * Get the tournaments that the user organizes.
     */
    public function organizedTournaments()
    {
        return $this->hasMany(Tournament::class, 'organizer_id');
    }
}

