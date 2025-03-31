<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.P
     */
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->date('dob')->nullable();
            $table->string('school')->nullable();
            $table->string('saturday_club')->nullable();
            $table->string('sunday_club')->nullable();
            $table->longText('previous_clubs')->nullable();
            $table->longText('playing_experience')->nullable();
            $table->string('preferred_position')->nullable();
            $table->string('other_positions')->nullable();
            $table->string('age_groups')->nullable();
            $table->longText('how_hear')->nullable();
            $table->longText('medical_conditions')->nullable();
            $table->longText('injuries')->nullable();
            $table->longText('additional_info')->nullable();
            $table->longText('notes')->nullable();
            $table->date('application_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
