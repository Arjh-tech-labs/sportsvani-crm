<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\Match;

class SuperAdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('superadmin');
    }

    /**
     * Show the super admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        $stats = [
            'users' => User::count(),
            'teams' => Team::count(),
            'tournaments' => Tournament::count(),
            'matches' => Match::count(),
        ];

        // Get recent activity
        $recentUsers = User::with('roles')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentTeams = Team::with('captain')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('superadmin.dashboard', compact('stats', 'recentUsers', 'recentTeams'));
    }

    /**
     * Show the users list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function users()
    {
        $users = User::with('roles')->paginate(15);
        return view('superadmin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function createUser()
    {
        $roles = Role::all();
        return view('superadmin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'mobile' => 'required|string|max:15|unique:users',
            'city' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'roles' => 'required|array',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->city = $request->city;
        $user->location = $request->location;
        $user->password = Hash::make($request->password);
        $user->user_id = User::generateUniqueUserId();
        $user->save();

        // Attach roles
        $user->roles()->attach($request->roles);

        return redirect()->route('superadmin.users')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function editUser($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        return view('superadmin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $id,
            'mobile' => 'required|string|max:15|unique:users,mobile,' . $id,
            'city' => 'required|string|max:255',
            'roles' => 'required|array',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->city = $request->city;
        $user->location = $request->location;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        // Sync roles
        $user->roles()->sync($request->roles);

        return redirect()->route('superadmin.users')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('superadmin.users')->with('success', 'User deleted successfully.');
    }

    /**
     * Show the teams list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function teams()
    {
        $teams = Team::with('captain')->paginate(15);
        return view('superadmin.teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new team.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function createTeam()
    {
        $users = User::all();
        return view('superadmin.teams.create', compact('users'));
    }

    /**
     * Store a newly created team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeTeam(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'captain_id' => 'required|exists:users,id',
            'logo' => 'nullable|image|max:2048',
        ]);

        $captain = User::findOrFail($request->captain_id);

        $team = new Team();
        $team->name = $request->name;
        $team->team_id = Team::generateUniqueTeamId();
        $team->captain_id = $request->captain_id;
        $team->captain_name = $captain->name;
        $team->captain_mobile = $captain->mobile;
        
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('team_logos', 'public');
            $team->logo = '/storage/' . $path;
        }
        
        $team->save();
        
        // Add captain as a player in the team
        $team->players()->attach($request->captain_id, ['role' => 'Captain']);

        return redirect()->route('superadmin.teams')->with('success', 'Team created successfully.');
    }

    /**
     * Show the form for editing the specified team.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function editTeam($id)
    {
        $team = Team::with('captain', 'players')->findOrFail($id);
        $users = User::all();
        return view('superadmin.teams.edit', compact('team', 'users'));
    }

    /**
     * Update the specified team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateTeam(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        $team = Team::findOrFail($id);
        $team->name = $request->name;
        
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('team_logos', 'public');
            $team->logo = '/storage/' . $path;
        }
        
        $team->save();

        return redirect()->route('superadmin.teams')->with('success', 'Team updated successfully.');
    }

    /**
     * Remove the specified team.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyTeam($id)
    {
        $team = Team::findOrFail($id);
        $team->delete();

        return redirect()->route('superadmin.teams')->with('success', 'Team deleted successfully.');
    }

    /**
     * Show the tournaments list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function tournaments()
    {
        $tournaments = Tournament::with('organizer')->paginate(15);
        return view('superadmin.tournaments.index', compact('tournaments'));
    }

    /**
     * Show the form for creating a new tournament.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function createTournament()
    {
        $users = User::all();
        return view('superadmin.tournaments.create', compact('users'));
    }

    /**
     * Store a newly created tournament.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeTournament(Request $request)
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

        $organizer = User::findOrFail($request->organizer_id);

        $tournament = new Tournament();
        $tournament->name = $request->name;
        $tournament->tournament_id = Tournament::generateUniqueTournamentId();
        $tournament->organizer_id = $request->organizer_id;
        $tournament->organizer_name = $organizer->name;
        $tournament->organizer_mobile = $organizer->mobile;
        $tournament->organizer_email = $organizer->email;
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
        
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('tournament_logos', 'public');
            $tournament->logo = '/storage/' . $path;
        }
        
        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('tournament_banners', 'public');
            $tournament->banner = '/storage/' . $path;
        }
        
        $tournament->save();

        return redirect()->route('superadmin.tournaments')->with('success', 'Tournament created successfully.');
    }

    /**
     * Show the form for editing the specified tournament.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function editTournament($id)
    {
        $tournament = Tournament::with('organizer', 'teams', 'groups', 'rounds')->findOrFail($id);
        $users = User::all();
        return view('superadmin.tournaments.edit', compact('tournament', 'users'));
    }

    /**
     * Update the specified tournament.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateTournament(Request $request, $id)
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
            'status' => 'required|string',
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
        $tournament->status = $request->status;
        
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('tournament_logos', 'public');
            $tournament->logo = '/storage/' . $path;
        }
        
        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('tournament_banners', 'public');
            $tournament->banner = '/storage/' . $path;
        }
        
        $tournament->save();

        return redirect()->route('superadmin.tournaments')->with('success', 'Tournament updated successfully.');
    }

    /**
     * Remove the specified tournament.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyTournament($id)
    {
        $tournament = Tournament::findOrFail($id);
        $tournament->delete();

        return redirect()->route('superadmin.tournaments')->with('success', 'Tournament deleted successfully.');
    }

    /**
     * Show the matches list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function matches()
    {
        $matches = Match::with(['teamA', 'teamB', 'tournament'])->paginate(15);
        return view('superadmin.matches.index', compact('matches'));
    }

    /**
     * Show the form for creating a new match.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function createMatch()
    {
        $teams = Team::all();
        $tournaments = Tournament::all();
        return view('superadmin.matches.create', compact('teams', 'tournaments'));
    }

    /**
     * Store a newly created match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeMatch(Request $request)
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

        $teamA = Team::findOrFail($request->team_a_id);
        $teamB = Team::findOrFail($request->team_b_id);

        $match = new Match();
        $match->match_id = Match::generateUniqueMatchId();
        $match->name = $request->name ?? $teamA->name . ' vs ' . $teamB->name;
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
        $match->save();

        return redirect()->route('superadmin.matches')->with('success', 'Match created successfully.');
    }

    /**
     * Show the form for editing the specified match.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function editMatch($id)
    {
        $match = Match::with(['teamA', 'teamB', 'tournament', 'round'])->findOrFail($id);
        $teams = Team::all();
        $tournaments = Tournament::all();
        $rounds = $match->tournament ? $match->tournament->rounds : collect();
        return view('superadmin.matches.edit', compact('match', 'teams', 'tournaments', 'rounds'));
    }

    /**
     * Update the specified match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateMatch(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'match_type' => 'required|string',
            'ball_type' => 'required|string',
            'pitch_type' => 'required|string',
            'overs' => 'required|integer|min:1',
            'powerplay_overs' => 'required|integer|min:1|lte:overs',
            'overs_per_bowler' => 'required|integer|min:1|lte:overs',
            'city' => 'required|string|max:255',
            'ground' => 'required|string|max:255',
            'date' => 'required|date',
            'status' => 'required|string',
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
        $match->status = $request->status;
        $match->save();

        return redirect()->route('superadmin.matches')->with('success', 'Match updated successfully.');
    }

    /**
     * Remove the specified match.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyMatch($id)
    {
        $match = Match::findOrFail($id);
        $match->delete();

        return redirect()->route('superadmin.matches')->with('success', 'Match deleted successfully.');
    }

    /**
     * Show the system settings.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function system()
    {
        return view('superadmin.system');
    }

    /**
     * Show the application settings.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function settings()
    {
        return view('superadmin.settings');
    }
}

