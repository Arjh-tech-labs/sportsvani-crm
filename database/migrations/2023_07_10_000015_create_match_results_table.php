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
        Schema::create('match_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->unique()->constrained()->onDelete('cascade');
            $table->foreignId('winner_id')->nullable()->constrained('teams')->onDelete('set null');
            $table->integer('win_margin')->nullable();
            $table->enum('win_margin_type', ['Runs', 'Wickets'])->nullable();
            $table->foreignId('man_of_the_match_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('summary')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_results');
    }
};

