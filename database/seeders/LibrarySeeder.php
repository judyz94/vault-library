<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\Author;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LibrarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create users
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('12345678%'),
            'library_id' => 'LIB-0001',
            'role' => UserRoleEnum::Admin->value,
        ]);

        $libraryUser = User::factory()->create([
            'name' => 'Library User',
            'email' => 'user@example.com',
            'password' => Hash::make('12345678%'),
            'library_id' => 'LIB-0002',
            'role' => UserRoleEnum::User->value,
        ]);

        // 2. Create authors
        $authorsData = [
            [
                'name' => 'Gabriel Garcia Marquez',
                'bio' => 'Colombian novelist, short-story writer, screenwriter, and journalist, known for magical realism.',
            ],
            [
                'name' => 'Hermann Hesse',
                'bio' => 'German-born Swiss poet, novelist, and painter, famous for works exploring spirituality and self-discovery.',
            ],
            [
                'name' => 'Ernest Hemingway',
                'bio' => 'American novelist and short story writer, awarded the Nobel Prize in Literature in 1954.',
            ],
            [
                'name' => 'Robert C. Martin',
                'bio' => 'American software engineer and author, widely known for his work on clean code and software craftsmanship.',
            ],
        ];

        $authors = [];
        foreach ($authorsData as $data) {
            $authors[] = Author::create($data);
        }

        // 3. Create books
        foreach ($authors as $author) {
            Book::factory(rand(2, 4))->create([
                'author_id' => $author->id,
            ]);
        }

        // 4. Create borrowings
        $users = [$adminUser, $libraryUser];
        $books = Book::all();

        foreach ($books as $book) {
            if (rand(0, 1)) {
                $borrowedAt = fake()->dateTimeBetween('-1 month', 'now');
                $dueAt = Carbon::parse($borrowedAt)->addDays(14);
                $returnedAt = rand(0, 1)
                    ? Carbon::parse($borrowedAt)->addDays(rand(1, 14))
                    : null;

                Borrowing::create([
                    'user_id' => $users[array_rand($users)]->id,
                    'book_id' => $book->id,
                    'borrowed_at' => $borrowedAt,
                    'due_at' => $dueAt,
                    'returned_at' => $returnedAt,
                ]);
            }
        }
    }
}
