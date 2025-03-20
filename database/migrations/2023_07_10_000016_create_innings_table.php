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
        Schema::create('innings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained()->onDelete('cascade');
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->integer('innings_number');
            $table->integer('runs')->default(0);
            $table->integer('wickets')->default(0);
            $table->decimal('overs', 5, 1)->default(0);
            $table->integer('extras_wides')->default(0);
            $table->integer('extras_no_balls')->default(0);
            $table->integer('extras_byes')->default(0);
            $table->integer('extras_leg_byes')->default(0);
            $table->integer('extras_penalty')->default(0);
            $table->integer('extras_total')->default(0);
            $table->unique(['match_id', 'team_id', 'innings_number']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('innings');
    }
};

