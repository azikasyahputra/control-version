<?php

namespace Database\Factories;

use App\Models\Objects;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Objects>
 */
class ObjectsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Objects::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => $this->faker->unique()->slug(3), // Generates a unique key like 'word-another-word'
            'value' => $this->faker->sentence(),      // Generates a random sentence for the value
        ];
    }
}
