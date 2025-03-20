<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Match;
use App\Models\Team;
use App\Models\Tournament;

class MatchController extends Controller
{
    /**
     * Get all matches.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $matches = Match::with(['teamA', 'teamB', 'tournament'])->get();
        
        return response()->json([
            'success' => true,
            'matches' => $matches,
        ]);
    }

    /**
     * Store a newly created match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'match_type' => 'required|string',
            'ball_type' => 'required|string',
            'pitch_type' => 'required|string',
            'overs' => 'required|integer|min:1',
            'powerplay_overs' => 'required|integer|min:1|lte:overs',
            'overs_per_bowler' => 'required|integer|min:1|lte:overs',
            'city' => 'required|string|max:255',
            'ground' => 'required|string|max:255',
            'date' => 'required|date',
            'team_a_id' => 'required|exists:teams,id',
            'team_b_id' => 'required|exists:teams,id|different:team_a_id',
            'tournament_id' => 'nullable|exists:tournaments,id',
            'round_id' => 'nullable|exists:tournament_rounds,id',
        ]);

        $match = new Match();
        $match->match_id = Match::generateUniqueMatchId();
        $match->match_type = $request->match_type;
        $match->ball_type = $request->ball_type;
        $match->pitch_type = $request->pitch_type;
        $match->overs = $request->overs;
        $match->powerplay_overs = $request->powerplay_overs;
        $match->overs_per_bowler = $request->overs_per_bowler;
        $match->city = $request->city;
        $match->ground = $request->ground;
        $match->date = $request->date;
        $match->team_a_id = $request->team_a_id;
        $match->team_b_id = $request->team_b_id;
        $match->tournament_id = $request->tournament_id;
        $match->round_id = $request->round_id;
        $match->status = 'Scheduled';
        
        // Generate match name if not provided
        if (!$request->name) {
            $teamA = Team::find($request->team_a_id);
            $teamB = Team::find($request->team_b_id);
            $match->name = $teamA->name . ' vs ' . $teamB->name;
        } else {
            $match->name = $request->name;
        }
        
        $match->save();

        return response()->json([
            'success' => true,
            'message' => 'Match created successfully',
            'match' => $match->load(['teamA', 'teamB', 'tournament']),
        ]);
    }

    /**
     * Display the specified match.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $match = Match::with([
            'teamA', 
            'teamB', 
            'tossWinner', 
            'tournament', 
            'round', 
            'officials', 
            'players', 
            'result', 
            'battingScorecard', 
            'bowlingScorecard', 
            'events', 
            'wagonWheel', 
            'liveStream', 
            'gallery'
        ])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'match' => $match,
        ]);
    }

    /**
     * Update the specified match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'match_type' => 'required|string',
            'ball_type' => 'required|string',
            'pitch_type' => 'required|string',
            'overs' => 'required|integer|min:1',
            'powerplay_overs' => 'required|integer|min:1|lte:overs',
            'overs_per_bowler' => 'required|integer|min:1|lte:overs',
            'city' => 'required|string|max:255',
            'ground' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $match = Match::findOrFail($id);
        $match->name = $request->name;
        $match->match_type = $request->match_type;
        $match->ball_type = $request->ball_type;
        $match->pitch_type = $request->pitch_type;
        $match->overs = $request->overs;
        $match->powerplay_overs = $request->powerplay_overs;
        $match->overs_per_bowler = $request->overs_per_bowler;
        $match->city = $request->city;
        $match->ground = $request->ground;
        $match->date = $request->date;
        $match->save();

        return response()->json([
            'success' => true,
            'message' => 'Match updated successfully',
            'match' => $match->load(['teamA', 'teamB', 'tournament']),
        ]);
    }

    /**
     * Remove the specified match.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $match = Match::findOrFail($id);
        $match->delete();

        return response()->json([
            'success' => true,
            'message' => 'Match deleted successfully',
        ]);
    }

    /**
     * Update the toss result.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateToss(Request $request, $id)
    {
        $request->validate([
            'toss_winner_id' => 'required|exists:teams,id',
            'toss_decision' => 'required|in:Bat,Bowl',
        ]);

        $match = Match::findOrFail($id);
        $match->toss_winner_id = $request->toss_winner_id;
        $match->toss_decision = $request->toss_decision;
        $match->save();

        return response()->json([
            'success' => true,
            'message' => 'Toss result updated successfully',
            'match' => $match->load(['teamA', 'teamB', 'tossWinner']),
        ]);
    }

    /**
     * Add an official to the match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addOfficial(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string',
        ]);

        $match = Match::findOrFail($id);
        
        // Check if official is already assigned to the match
        if ($match->officials()->where('user_id', $request->user_id)->where('role', $request->role)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Official is already assigned to the match',
            ], 400);
        }
        
        // Add official to the match
        $match->officials()->attach($request->user_id, [
            'role' => $request->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Official added to the match successfully',
        ]);
    }

    /**
     * Add a player to the match squad.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addPlayer(Request $request, $id)
    {
        $request->validate([
            'player_id' => 'required|exists:users,id',
            'team_id' => 'required|exists:teams,id',
            'role' => 'nullable|string',
        ]);

        $match = Match::findOrFail($id);
        
        // Check if player is already in the squad
        if ($match->players()->where('user_id', $request->player_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Player is already in the match squad',
            ], 400);
        }
        
        // Add player to the match squad
        $match->players()->attach($request->player_id, [
            'team_id' => $request->team_id,
            'role' => $request->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Player added to the match squad successfully',
        ]);
    }

    /**
     * Start the match.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function startMatch($id)
    {
        $match = Match::findOrFail($id);
        
        // Check if match can be started
        if ($match->status !== 'Scheduled') {
            return response()->json([
                'success' => false,
                'message' => 'Match cannot be started',
            ], 400);
        }
        
        // Update match status
        $match->status = 'Live';
        $match->save();

        return response()->json([
            'success' => true,
            'message' => 'Match started successfully',
            'match' => $match->load(['teamA', 'teamB']),
        ]);
    }

    /**
     * End the match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function endMatch(Request $request, $id)
    {
        $request->validate([
            'winner_id' => 'nullable|exists:teams,id',
            'win_margin' => 'nullable|integer',
            'win_margin_type' => 'nullable|in:Runs,Wickets',
            'man_of_the_match_id' => 'nullable|exists:users,id',
            'summary' => 'nullable|string',
        ]);

        $match = Match::findOrFail($id);
        
        // Check if match can be ended
        if ($match->status !== 'Live') {
            return response()->json([
                'success' => false,
                'message' => 'Match cannot be ended',
            ], 400);
        }
        
        // Update match status
        $match->status = 'Completed';
        $match->save();
        
        // Create match result
        $match->result()->create([
            'winner_id' => $request->winner_id,
            'win_margin' => $request->win_margin,
            'win_margin_type' => $request->win_margin_type,
            'man_of_the_match_id' => $request->man_of_the_match_id,
            'summary' => $request->summary,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Match ended successfully',
            'match' => $match->load(['teamA', 'teamB', 'result']),
        ]);
    }
}

