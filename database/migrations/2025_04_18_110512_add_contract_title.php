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
        Schema::table('player_contract_signatures', function (Blueprint $table) {
            $table->string('contract_name')->before('contract_content')->default('Player Contract');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_contract_signatures', function (Blueprint $table) {
            $table->dropColumn('contract_name');
        });
    }
};
