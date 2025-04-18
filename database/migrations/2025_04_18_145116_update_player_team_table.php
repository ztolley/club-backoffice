<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add a new team named alternate_team_id to store the team a player is dual signed with
        Schema::table('players', function (Blueprint $table) {
            $table->uuid('alternate_team_id')->nullable()->after('team_id')->references('id')->on('teams');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_team');

        Schema::table('players', function (Blueprint $table) {
            $table->dropForeign(['alternate_team_id']);
            $table->dropColumn('alternate_team_id');
        });
    }
};
