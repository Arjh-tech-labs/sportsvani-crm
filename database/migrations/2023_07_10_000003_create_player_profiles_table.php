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
        Schema::create('player_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->enum('player_type', ['Batter', 'Bowler', 'Allrounder', 'WicketKeeper']);
            $table->enum('batting_style', ['Right', 'Left'])->nullable();
            $table->enum('bowling_style', [
                'Right Arm Fast', 
                'Left Arm Fast', 
                'Right Arm Medium Pacer', 
                'Left Arm Medium Pacer', 
                'Right Arm Off Spin', 
                'Right Arm Leg Spin', 
                'Left Arm Orthodox Spin', 
                'Left Arm Unorthodox Spin'
            ])->nullable();
            $table->integer('highest_score')->default(0);
            $table->string('best_bowling', 10)->default('0/0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_profiles');
    }
};

