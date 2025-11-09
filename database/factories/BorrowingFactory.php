<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Borrowing>
 */
class BorrowingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $borrowedAt = $this->faker->dateTimeBetween('-1 month', 'now');
        $dueAt = Carbon::parse($borrowedAt)->addDays(14);

        $returnedAt = $this->faker->boolean(50)
            ? Carbon::parse($borrowedAt)->addDays(rand(1, 14))
            : null;

        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'borrowed_at' => $borrowedAt,
            'due_at' => $dueAt,
            'returned_at' => $returnedAt,
        ];
    }
}
