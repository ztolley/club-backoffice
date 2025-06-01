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
        // Remove foreign key and column from players table
        Schema::table('players', function (Blueprint $table) {
            $table->dropForeign(['applicant_id']);
            $table->dropColumn('applicant_id');
        });

        // Remove foreign key index from applicants table
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropForeign('applicants_player_id_foreign');
            $table->dropColumn('player_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add the column and foreign key back if you need to rollback
        Schema::table('players', function (Blueprint $table) {
            $table->uuid('applicant_id')->nullable();
            $table->foreign('applicant_id')->references('id')->on('applicants');
        });

        Schema::table('applicants', function (Blueprint $table) {
            $table->uuid('player_id')->nullable()->after('id'); // Assuming UUID is used
            $table->foreign('player_id')->references('id')->on('players')->onDelete('set null');
        });
    }
};
