<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\MatchController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home page
Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::get('/superadmin/login', [AuthController::class, 'showSuperAdminLoginForm'])->name('superadmin.login');
Route::post('/superadmin/login', [AuthController::class, 'superAdminLogin']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/mobile-verification', [AuthController::class, 'showMobileVerificationForm'])->name('mobile.verification');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Super Admin routes
Route::prefix('superadmin')->middleware(['auth', 'superadmin'])->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');
    
    // User management
    Route::get('/users', [SuperAdminController::class, 'users'])->name('superadmin.users');
    Route::get('/users/create', [SuperAdminController::class, 'createUser'])->name('superadmin.users.create');
    Route::post('/users', [SuperAdminController::class, 'storeUser'])->name('superadmin.users.store');
    
    // Team management
    Route::get('/teams', [SuperAdminController::class, 'teams'])->name('superadmin.teams');
    
    // Tournament management
    Route::get('/tournaments', [SuperAdminController::class, 'tournaments'])->name('superadmin.tournaments');
    
    // Match management
    Route::get('/matches', [SuperAdminController::class, 'matches'])->name('superadmin.matches');
    
    // System settings
    Route::get('/system', [SuperAdminController::class, 'system'])->name('superadmin.system');
    
    // Application settings
    Route::get('/settings', [SuperAdminController::class, 'settings'])->name('superadmin.settings');
});

// User routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    
    // Player profile
    Route::get('/player-profile', [UserController::class, 'playerProfile'])->name('player.profile');
    Route::put('/player-profile', [UserController::class, 'updatePlayerProfile'])->name('player.profile.update');
    
    // Team management
    Route::get('/teams', [TeamController::class, 'index'])->name('teams');
    Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');
    Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
    Route::get('/teams/{id}', [TeamController::class, 'show'])->name('teams.show');
    Route::get('/teams/{id}/edit', [TeamController::class, 'edit'])->name('teams.edit');
    Route::put('/teams/{id}', [TeamController::class, 'update'])->name('teams.update');
    
    // Tournament management
    Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments');
    Route::get('/tournaments/create', [TournamentController::class, 'create'])->name('tournaments.create');
    Route::post('/tournaments', [TournamentController::class, 'store'])->name('tournaments.store');
    Route::get('/tournaments/{id}', [TournamentController::class, 'show'])->name('tournaments.show');
    Route::get('/tournaments/{id}/edit', [TournamentController::class, 'edit'])->name('tournaments.edit');
    Route::put('/tournaments/{id}', [TournamentController::class, 'update'])->name('tournaments.update');
    
    // Match management
    Route::get('/matches', [MatchController::class, 'index'])->name('matches');
    Route::get('/matches/create', [MatchController::class, 'create'])->name('matches.create');
    Route::post('/matches', [MatchController::class, 'store'])->name('matches.store');
    Route::get('/matches/{id}', [MatchController::class, 'show'])->name('matches.show');
    Route::get('/matches/{id}/edit', [MatchController::class, 'edit'])->name('matches.edit');
    Route::put('/matches/{id}', [MatchController::class, 'update'])->name('matches.update');
    Route::get('/matches/{id}/scoring', [MatchController::class, 'scoring'])->name('matches.scoring');
});

