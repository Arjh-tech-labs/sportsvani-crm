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
    Route::get('/users/{id}/edit', [SuperAdminController::class, 'editUser'])->name('superadmin.users.edit');
    Route::put('/users/{id}', [SuperAdminController::class, 'updateUser'])->name('superadmin.users.update');
    Route::delete('/users/{id}', [SuperAdminController::class, 'destroyUser'])->name('superadmin.users.destroy');
    
    // Team management
    Route::get('/teams', [SuperAdminController::class, 'teams'])->name('superadmin.teams');
    Route::get('/teams/create', [SuperAdminController::class, 'createTeam'])->name('superadmin.teams.create');
    Route::post('/teams', [SuperAdminController::class, 'storeTeam'])->name('superadmin.teams.store');
    Route::get('/teams/{id}/edit', [SuperAdminController::class, 'editTeam'])->name('superadmin.teams.edit');
    Route::put('/teams/{id}', [SuperAdminController::class, 'updateTeam'])->name('superadmin.teams.update');
    Route::delete('/teams/{id}', [SuperAdminController::class, 'destroyTeam'])->name('superadmin.teams.destroy');
    
    // Tournament management
    Route::get('/tournaments', [SuperAdminController::class, 'tournaments'])->name('superadmin.tournaments');
    Route::get('/tournaments/create', [SuperAdminController::class, 'createTournament'])->name('superadmin.tournaments.create');
    Route::post('/tournaments', [SuperAdminController::class, 'storeTournament'])->name('superadmin.tournaments.store');
    Route::get('/tournaments/{id}/edit', [SuperAdminController::class, 'editTournament'])->name('superadmin.tournaments.edit');
    Route::put('/tournaments/{id}', [SuperAdminController::class, 'updateTournament'])->name('superadmin.tournaments.update');
    Route::delete('/tournaments/{id}', [SuperAdminController::class, 'destroyTournament'])->name('superadmin.tournaments.destroy');
    
    // Match management
    Route::get('/matches', [SuperAdminController::class, 'matches'])->name('superadmin.matches');
    Route::get('/matches/create', [SuperAdminController::class, 'createMatch'])->name('superadmin.matches.create');
    Route::post('/matches', [SuperAdminController::class, 'storeMatch'])->name('superadmin.matches.store');
    Route::get('/matches/{id}/edit', [SuperAdminController::class, 'editMatch'])->name('superadmin.matches.edit');
    Route::put('/matches/{id}', [SuperAdminController::class, 'updateMatch'])->name('superadmin.matches.update');
    Route::delete('/matches/{id}', [SuperAdminController::class, 'destroyMatch'])->name('superadmin.matches.destroy');
    
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
});

