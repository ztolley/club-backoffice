<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Applicant;

class ApplicantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Applicant::class;
    protected static int $emailCounter = 1;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $email = 'zac+' . static::$emailCounter++ . '@thetolleys.com';

        return [
            'name' => fake()->name(),
            'address' => fake()->address(),
            'email' => $email,
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
            'additional_info' => fake()->text()
        ];
    }
}
