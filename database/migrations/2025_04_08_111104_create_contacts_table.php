<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create the contacts table
        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->longText('address')->nullable();
            $table->timestamps();
        });

        Schema::create('contact_player', function (Blueprint $table) {
            $table->uuid('player_id');
            $table->uuid('contact_id');
            $table->boolean('is_primary')->default(false);
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->primary(['player_id', 'contact_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_player');
        Schema::dropIfExists('contacts');
    }
};
