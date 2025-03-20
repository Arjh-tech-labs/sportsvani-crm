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
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('tournament_id', 20)->unique();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade');
            $table->string('organizer_name');
            $table->string('organizer_mobile', 15);
            $table->string('organizer_email')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('category', ['Open', 'Corporate', 'Community', 'School', 'College', 'University Series', 'Other']);
            $table->enum('ball_type', ['Leather', 'Tennis', 'Other']);
            $table->enum('pitch_type', ['Rough', 'Cement', 'Turf', 'Matt', 'Other']);
            $table->enum('match_type', ['Limited Overs', 'Box/Turf', 'Test Match']);
            $table->integer('team_count');
            $table->decimal('fees', 10, 2)->nullable();
            $table->enum('winning_prize', ['Cash', 'Trophy', 'Both'])->nullable();
            $table->json('match_days')->nullable();
            $table->enum('match_timings', ['Day', 'Night', 'Day & Night'])->nullable();
            $table->enum('format', ['League', 'Knockout']);
            $table->enum('status', ['Upcoming', 'Active', 'Completed', 'Cancelled'])->default('Upcoming');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};

