<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
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

        return view('superadmin.dashboard', compact('stats'));
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
        return view('superadmin.users.create');
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
            'email' => 'required|string|email|max:255|unique:users',
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

