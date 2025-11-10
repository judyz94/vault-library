<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'author_id' => Author::factory(),
            'isbn' => $this->faker->unique()->isbn13(),
            'publication_year' => $this->faker->numberBetween(1900, now()->year),
            'available' => $this->faker->boolean(80),
        ];
    }
}
