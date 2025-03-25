<?php

namespace Database\Factories;

use App\Models\Mutation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mutation>
 */
class MutationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
          'title' => $this->faker->words(3),
          'remarks' => $this->faker->text(128),
        ];
    }
}
