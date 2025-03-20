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
        Schema::create('player_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->unique()->constrained('users')->onDelete('cascade');
            $table->integer('matches')->default(0);
            $table->integer('runs')->default(0);
            $table->integer('wickets')->default(0);
            $table->integer('overs')->default(0);
            $table->integer('balls_faced')->default(0);
            $table->decimal('average', 8, 2)->default(0);
            $table->decimal('economy', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_stats');
    }
};

