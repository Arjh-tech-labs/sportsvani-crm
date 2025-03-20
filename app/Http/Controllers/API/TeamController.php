<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\User;

class TeamController extends Controller
{
    /**
     * Get all teams.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teams = Team::with('captain')->get();
        
        return response()->json([
            'success' => true,
            'teams' => $teams,
        ]);
    }

    /**
     * Store a newly created team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'captain_id' => 'required|exists:users,id',
            'logo' => 'nullable|image|max:2048',
        ]);

        $team = new Team();
        $team->name = $request->name;
        $team->team_id = Team::generateUniqueTeamId();
        $team->captain_id = $request->captain_id;
        
        // Get captain details
        $captain = User::find($request->captain_id);
        $team->captain_name = $captain->name;
        $team->captain_mobile = $captain->mobile;
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('team_logos', 'public');
            $team->logo = '/storage/' . $path;
        }
        
        $team->save();
        
        // Add captain as a player in the team
        $team->players()->attach($request->captain_id);

        return response()->json([
            'success' => true,
            'message' => 'Team created successfully',
            'team' => $team->load('captain'),
        ]);
    }

    /**
     * Display the specified team.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $team = Team::with(['captain', 'players', 'managers', 'stats', 'tournaments', 'matches', 'awards', 'gallery'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'team' => $team,
        ]);
    }

    /**
     * Update the specified team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'captain_id' => 'required|exists:users,id',
            'logo' => 'nullable|image|max:2048',
        ]);

        $team = Team::findOrFail($id);
        $team->name = $request->name;
        $team->captain_id = $request->captain_id;
        
        // Get captain details
        $captain = User::find($request->captain_id);
        $team->captain_name = $captain->name;
        $team->captain_mobile = $captain->mobile;
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('team_logos', 'public');
            $team->logo = '/storage/' . $path;
        }
        
        $team->save();

        return response()->json([
            'success' => true,
            'message' => 'Team updated successfully',
            'team' => $team->load('captain'),
        ]);
    }

    /**
     * Remove the specified team.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $team = Team::findOrFail($id);
        $team->delete();

        return response()->json([
            'success' => true,
            'message' => 'Team deleted successfully',
        ]);
    }

    /**
     * Add a player to the team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addPlayer(Request $request, $id)
    {
        $request->validate([
            'player_id' => 'required|exists:users,id',
        ]);

        $team = Team::findOrFail($id);
        
        // Check if player is already in the team
        if ($team->players()->where('player_id', $request->player_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Player is already in the team',
            ], 400);
        }
        
        // Add player to the team
        $team->players()->attach($request->player_id);

        return response()->json([
            'success' => true,
            'message' => 'Player added to the team successfully',
        ]);
    }

    /**
     * Remove a player from the team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removePlayer(Request $request, $id)
    {
        $request->validate([
            'player_id' => 'required|exists:users,id',
        ]);

        $team = Team::findOrFail($id);
        
        // Check if player is the captain
        if ($team->captain_id == $request->player_id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot remove the captain from the team',
            ], 400);
        }
        
        // Remove player from the team
        $team->players()->detach($request->player_id);

        return response()->json([
            'success' => true,
            'message' => 'Player removed from the team successfully',
        ]);
    }
}

