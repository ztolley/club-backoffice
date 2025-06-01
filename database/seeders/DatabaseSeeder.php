<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        # Clear Existing data
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate pivot tables first
        DB::table('contact_player')->truncate();

        // Then truncate the main tables
        \App\Models\Player::truncate();
        \App\Models\Contact::truncate();
        \App\Models\Team::truncate();
        \App\Models\Applicant::truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        # Create 60 Applicants
        \App\Models\Applicant::factory()->count(200)->create();

        // Create 5 teams (U12, U13, U14, U15, U16)
        for ($i = 12; $i <= 16; $i++) {
            \App\Models\Team::factory()->create([
                'name' => 'U' . $i,
            ]);
        }

        # Create 100 Players with a random team assignment
        \App\Models\Player::factory()->count(100)->create([
            'team_id' => function () {
                return \App\Models\Team::inRandomOrder()->first()->id;
            }
        ]);
    }
}
