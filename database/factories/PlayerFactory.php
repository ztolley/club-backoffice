<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Player;
use App\Models\Team;

class PlayerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Player::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'parent_carer_names' => fake()->text(),
            'address' => fake()->word(),
            'postal_code' => fake()->postcode(),
            'primary_email' => fake()->word(),
            'primary_phone' => fake()->word(),
            'dob' => fake()->date(),
            'preferred_position' => fake()->word(),
            'other_positions' => fake()->word(),
            'medical_conditions' => fake()->text(),
            'injuries' => fake()->text(),
            'additional_info' => fake()->text(),
            'allowed_marketing' => fake()->boolean(),
            'allowed_photography' => fake()->boolean(),
            'agreed_player_code' => fake()->boolean(),
            'agreed_parent_code' => fake()->boolean(),
            'signed_date' => fake()->date(),
            'team_id' => Team::factory(),
            'applicant_id' => fake()->word(),
        ];
    }
}
