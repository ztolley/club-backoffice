<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
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
            'name' => fake()->name(),
            'address' => fake()->address(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'dob' => fake()->date(),
            'parent_carer_names' => fake()->name(),
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
            'team_id' => Team::factory()
        ];
    }
}
