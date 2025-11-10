<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class BorrowingService
{
    /**
     * @throws Exception
     */
    public function borrow(User $user, int $bookId): Borrowing
    {
        $book = Book::findOrFail($bookId);

        // Validates if the book is currently available
        if (!$book->available) {
            throw new Exception('This book is currently unavailable.');
        }

        $activeBorrowings = Borrowing::where('user_id', $user->id)
            ->whereNull('returned_at')
            ->count();

        // User cannot have more than 3 active borrowings
        if ($activeBorrowings >= 3) {
            throw new Exception('User already has 3 borrowed books.');
        }

        // Create borrowing record
        $borrowing = Borrowing::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
            'due_at' => Carbon::now()->addDays(14),
        ]);

        // Mark the borrowed book as unavailable
        $book->update(['available' => false]);

        return $borrowing;
    }

    public function return(User $user, int $bookId): Borrowing
    {
        $borrowing = Borrowing::with('book')
            ->where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->whereNull('returned_at')
            ->firstOrFail();

        $borrowing->update(['returned_at' => Carbon::now()]);

        // Mark the returned book as available
        $borrowing->book->update(['available' => true]);

        return $borrowing;
    }

    public function userBorrowed(User $user): Collection
    {
        return Borrowing::with('book')
            ->where('user_id', $user->id)
            ->whereNull('returned_at')
            ->get();
    }
}
