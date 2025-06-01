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
    protected static int $emailCounter = 1;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $email = 'zac+' . static::$emailCounter++ . '@thetolleys.com';

        # Preferred position should be 'CB', 'RB', 'LB', 'CM', 'RM', 'LM', 'CF', 'RW', 'LW'
        $preferredPositions = ['CB', 'RB', 'LB', 'CM', 'RM', 'LM', 'CF', 'RW', 'LW'];
        $preferredPosition = $preferredPositions[array_rand($preferredPositions)];

        return [
            'additional_info' => fake()->text(),
            'agreed_parent_code' => fake()->boolean(),
            'agreed_player_code' => fake()->boolean(),
            'allowed_marketing' => fake()->boolean(),
            'dob' => fake()->date(),
            'fan' => fake()->unique()->numberBetween(100000, 999999),
            'injuries' => fake()->text(),
            'medical_conditions' => fake()->text(),
            'name' => fake()->name(),
            'other_positions' => "",
            'preferred_position' => $preferredPosition,
            'signed_date' => now(),
            'team_id' => Team::factory()
        ];
    }
}
