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
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('address');
            $table->string('postal_code');
            $table->string('email');
            $table->string('phone');
            $table->date('dob');
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
