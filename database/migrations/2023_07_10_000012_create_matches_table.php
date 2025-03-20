<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->string('match_id', 20)->unique();
            $table->string('name');
            $table->enum('match_type', ['Limited Overs', 'Test']);
            $table->enum('ball_type', ['Leather', 'Tennis', 'Other']);
            $table->enum('pitch_type', ['Rough', 'Matt', 'Cement', 'Turf', 'Other']);
            $table->integer('overs');
            $table->integer('powerplay_overs');
            $table->integer('overs_per_bowler');
            $table->string('city');
            $table->string('ground');
            $table->dateTime('date');
            $table->foreignId('team_a_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('team_b_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('toss_winner_id')->nullable()->constrained('teams')->onDelete('set null');
            $table->enum('toss_decision', ['Bat', 'Bowl'])->nullable();
            $table->foreignId('tournament_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('round_id')->nullable()->constrained('tournament_rounds')->onDelete('set null');
            $table->enum('status', ['Scheduled', 'Live', 'Completed', 'Abandoned', 'Cancelled'])->default('Scheduled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};

