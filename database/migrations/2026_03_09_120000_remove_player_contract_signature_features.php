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
        if (Schema::hasTable('player_contract_signatures')) {
            Schema::drop('player_contract_signatures');
        }

        if (!Schema::hasTable('players')) {
            return;
        }

        $columns = array_values(array_filter([
            'agreed_player_code',
            'agreed_parent_code',
        ], fn(string $column) => Schema::hasColumn('players', $column)));

        if ($columns !== []) {
            Schema::table('players', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('players')) {
            Schema::table('players', function (Blueprint $table) {
                if (!Schema::hasColumn('players', 'agreed_player_code')) {
                    $table->boolean('agreed_player_code')->default(false);
                }

                if (!Schema::hasColumn('players', 'agreed_parent_code')) {
                    $table->boolean('agreed_parent_code')->default(false);
                }
            });
        }

        if (!Schema::hasTable('player_contract_signatures')) {
            Schema::create('player_contract_signatures', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('player_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->string('fan');
                $table->string('contract_name')->default('Player Contract');
                $table->longText('contract_content');
                $table->text('signature_base64');
                $table->timestamp('submitted_at');
                $table->timestamps();
            });
        }
    }
};
