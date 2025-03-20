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
        Schema::create('team_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->unique()->constrained()->onDelete('cascade');
            $table->integer('matches')->default(0);
            $table->integer('won')->default(0);
            $table->integer('lost')->default(0);
            $table->integer('tied')->default(0);
            $table->integer('drawn')->default(0);
            $table->decimal('win_percentage', 5, 2)->default(0);
            $table->integer('toss_won')->default(0);
            $table->integer('bat_first')->default(0);
            $table->integer('no_result')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_stats');
    }
};

