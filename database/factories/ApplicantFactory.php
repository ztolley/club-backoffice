<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Applicant;

class ApplicantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Applicant::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'address' => fake()->word(),
            'postal_code' => fake()->postcode(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'dob' => fake()->date(),
            'school' => fake()->word(),
            'saturday_club' => fake()->word(),
            'sunday_club' => fake()->word(),
            'previous_clubs' => fake()->text(),
            'playing_experience' => fake()->text(),
            'preferred_position' => fake()->word(),
            'other_positions' => fake()->word(),
            'age_groups' => fake()->word(),
            'how_hear' => fake()->text(),
            'medical_conditions' => fake()->text(),
            'injuries' => fake()->text(),
            'additional_info' => fake()->text(),
            'player_id' => fake()->word(),
        ];
    }
}
