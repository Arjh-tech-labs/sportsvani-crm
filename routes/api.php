<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\TeamController;
use App\Http\Controllers\API\TournamentController;
use App\Http\Controllers\API\MatchController;
use App\Http\Controllers\API\PlayerProfileController;
use App\Http\Controllers\API\MatchScoringController;
use App\Http\Controllers\API\FirebaseAuthController;
use App\Http\Controllers\API\YouTubeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes
Route::post('auth/firebase/send-otp', [FirebaseAuthController::class, 'sendOTP']);
Route::post('auth/firebase/verify-otp', [FirebaseAuthController::class, 'verifyOTP']);

// Super Admin Authentication
Route::post('auth/superadmin/login', [AuthController::class, 'superAdminLogin']);

// User routes
Route::apiResource('users', UserController::class);

// Team routes
Route::apiResource('teams', TeamController::class);
Route::post('teams/{id}/add-player', [TeamController::class, 'addPlayer']);
Route::post('teams/{id}/remove-player', [TeamController::class, 'removePlayer']);

// Tournament routes
Route::apiResource('tournaments', TournamentController::class);
Route::post('tournaments/{id}/add-team', [TournamentController::class, 'addTeam']);
Route::post('tournaments/{id}/remove-team', [TournamentController::class, 'removeTeam']);
Route::post('tournaments/{id}/create-group', [TournamentController::class, 'createGroup']);
Route::post('tournaments/{id}/create-round', [TournamentController::class, 'createRound']);

// Match routes
Route::apiResource('matches', MatchController::class);
Route::post('matches/{id}/update-toss', [MatchController::class, 'updateToss']);
Route::post('matches/{id}/add-official', [MatchController::class, 'addOfficial']);
Route::post('matches/{id}/add-player', [MatchController::class, 'addPlayer']);
Route::post('matches/{id}/start', [MatchController::class, 'startMatch']);
Route::post('matches/{id}/end', [MatchController::class, 'endMatch']);

// Player Profile routes
Route::apiResource('player-profiles', PlayerProfileController::class);

// Match Scoring routes
Route::post('match-scoring', [MatchScoringController::class, 'recordEvent']);
Route::get('match-scoring/{id}', [MatchScoringController::class, 'getScorecard']);

// YouTube API routes
Route::post('youtube/create-stream', [YouTubeController::class, 'createStream']);
Route::get('youtube/match-videos', [YouTubeController::class, 'getMatchVideos']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

