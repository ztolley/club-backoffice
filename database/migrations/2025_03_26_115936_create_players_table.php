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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('email');
            $table->string('phone');
            $table->date('dob');
            $table->longText('parent_carer_names');
            $table->string('preferred_position')->nullable();
            $table->string('other_positions')->nullable();
            $table->longText('medical_conditions')->nullable();
            $table->longText('injuries')->nullable();
            $table->longText('additional_info')->nullable();
            $table->boolean('allowed_marketing');
            $table->boolean('allowed_photography');
            $table->boolean('agreed_player_code');
            $table->boolean('agreed_parent_code');
            $table->date('signed_date')->nullable();
            $table->foreignId('team_id')->nullable();
            $table->foreignId('applicant_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
