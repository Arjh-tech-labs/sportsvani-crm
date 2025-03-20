<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tournament;
use App\Models\User;

class TournamentController extends Controller
{
    /**
     * Get all tournaments.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tournaments = Tournament::with('organizer')->get();
        
        return response()->json([
            'success' => true,
            'tournaments' => $tournaments,
        ]);
    }

    /**
     * Store a newly created tournament.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'organizer_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'category' => 'required|string',
            'ball_type' => 'required|string',
            'pitch_type' => 'required|string',
            'match_type' => 'required|string',
            'team_count' => 'required|integer|min:4',
            'format' => 'required|string',
            'logo' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:2048',
        ]);

        $tournament = new Tournament();
        $tournament->name = $request->name;
        $tournament->tournament_id = Tournament::generateUniqueTournamentId();
        $tournament->organizer_id = $request->organizer_id;
        $tournament->start_date = $request->start_date;
        $tournament->end_date = $request->end_date;
        $tournament->category = $request->category;
        $tournament->ball_type = $request->ball_type;
        $tournament->pitch_type = $request->pitch_type;
        $tournament->match_type = $request->match_type;
        $tournament->team_count = $request->team_count;
        $tournament->format = $request->format;
        $tournament->fees = $request->fees;
        $tournament->winning_prize = $request->winning_prize;
        $tournament->match_days = $request->match_days;
        $tournament->match_timings = $request->match_timings;
        $tournament->status = 'Upcoming';
        
        // Get organizer details
        $organizer = User::find($request->organizer_id);
        $tournament->organizer_name = $organizer->name;
        $tournament->organizer_mobile = $organizer->mobile;
        $tournament->organizer_email = $organizer->email;
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('tournament_logos', 'public');
            $tournament->logo = '/storage/' . $path;
        }
        
        // Handle banner upload
        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('tournament_banners', 'public');
            $tournament->banner = '/storage/' . $path;
        }
        
        $tournament->save();

        return response()->json([
            'success' => true,
            'message' => 'Tournament created successfully',
            'tournament' => $tournament->load('organizer'),
        ]);
    }

    /**
     * Display the specified tournament.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tournament = Tournament::with([
            'organizer', 
            'teams', 
            'groups', 
            'rounds', 
            'officials', 
            'matches', 
            'pointsTable', 
            'leaderboard', 
            'gallery'
        ])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'tournament' => $tournament,
        ]);
    }

    /**
     * Update the specified tournament.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'category' => 'required|string',
            'ball_type' => 'required|string',
            'pitch_type' => 'required|string',
            'match_type' => 'required|string',
            'team_count' => 'required|integer|min:4',
            'format' => 'required|string',
            'logo' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:2048',
        ]);

        $tournament = Tournament::findOrFail($id);
        $tournament->name = $request->name;
        $tournament->start_date = $request->start_date;
        $tournament->end_date = $request->end_date;
        $tournament->category = $request->category;
        $tournament->ball_type = $request->ball_type;
        $tournament->pitch_type = $request->pitch_type;
        $tournament->match_type = $request->match_type;
        $tournament->team_count = $request->team_count;
        $tournament->format = $request->format;
        $tournament->fees = $request->fees;
        $tournament->winning_prize = $request->winning_prize;
        $tournament->match_days = $request->match_days;
        $tournament->match_timings = $request->match_timings;
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('tournament_logos', 'public');
            $tournament->logo = '/storage/' . $path;
        }
        
        // Handle banner upload
        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('tournament_banners', 'public');
            $tournament->banner = '/storage/' . $path;
        }
        
        $tournament->save();

        return response()->json([
            'success' => true,
            'message' => 'Tournament updated successfully',
            'tournament' => $tournament->load('organizer'),
        ]);
    }

    /**
     * Remove the specified tournament.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tournament = Tournament::findOrFail($id);
        $tournament->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tournament deleted successfully',
        ]);
    }

    /**
     * Add a team to the tournament.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addTeam(Request $request, $id)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'group_id' => 'nullable|exists:tournament_groups,id',
        ]);

        $tournament = Tournament::findOrFail($id);
        
        // Check if team is already in the tournament
        if ($tournament->teams()->where('team_id', $request->team_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Team is already in the tournament',
            ], 400);
        }
        
        // Check if tournament has reached team limit
        if ($tournament->teams()->count() >= $tournament->team_count) {
            return response()->json([
                'success' => false,
                'message' => 'Tournament has reached the maximum number of teams',
            ], 400);
        }
        
        // Add team to the tournament
        $tournament->teams()->attach($request->team_id, [
            'group_id' => $request->group_id,
            'status' => 'Approved',
            'registered_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Team added to the tournament successfully',
        ]);
    }

    /**
     * Remove a team from the tournament.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeTeam(Request $request, $id)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
        ]);

        $tournament = Tournament::findOrFail($id);
        
        // Remove team from the tournament
        $tournament->teams()->detach($request->team_id);

        return response()->json([
            'success' => true,
            'message' => 'Team removed from the tournament successfully',
        ]);
    }

    /**
     * Create a group in the tournament.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createGroup(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tournament = Tournament::findOrFail($id);
        
        // Create a new group
        $group = $tournament->groups()->create([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Group created successfully',
            'group' => $group,
        ]);
    }

    /**
     * Create a round in the tournament.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createRound(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $tournament = Tournament::findOrFail($id);
        
        // Create a new round
        $round = $tournament->rounds()->create([
            'name' => $request->name,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Round created successfully',
            'round' => $round,
        ]);
    }
}

