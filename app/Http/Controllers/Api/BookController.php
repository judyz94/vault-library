<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Books\StoreBookRequest;
use App\Http\Requests\Books\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $books = Book::with('author')->paginate(10);

        return $this->success(
            BookResource::collection($books),
            'Books retrieved successfully.'
        );
    }

    public function store(StoreBookRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $book = Book::create($validated);

            return $this->success(
                new BookResource($book),
                'Book created successfully.',
                201
            );
        } catch (QueryException $e) {
            return $this->error('Database error while creating book.', 400, $e->getMessage());
        } catch (Exception $e) {
            return $this->error('Unexpected error while creating book.', 500, $e->getMessage());
        }
    }

    public function show(Book $book): JsonResponse
    {
        $book->load('author');

        return $this->success(
            new BookResource($book),
            'Book retrieved successfully.'
        );
    }

    public function update(UpdateBookRequest $request, Book $book): JsonResponse
    {
        try {
            $validated = $request->validated();

            $book->update($validated);

            return $this->success(
                new BookResource($book),
                'Book updated successfully.'
            );
        } catch (QueryException $e) {
            return $this->error('Database error while updating book.', 400, $e->getMessage());
        } catch (Exception $e) {
            return $this->error('Unexpected error while updating book.', 500, $e->getMessage());
        }
    }

    public function destroy(Book $book): JsonResponse
    {
        $book->delete();

        return $this->success(null, 'Book deleted successfully.');
    }

    /**
     * Search books by title, author name, or ISBN.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->query('q');

        if (!$query) {
            return $this->error('Query parameter "q" is required.', 400);
        }

        $books = Book::with('author')
            ->where('title', 'like', "%{$query}%")
            ->orWhereHas('author', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->orWhere('isbn', 'like', "%{$query}%")
            ->paginate(10);

        if ($books->isEmpty()) {
            return $this->error('No books found matching your search.', 404);
        }

        return $this->success(
            BookResource::collection($books),
            'Books retrieved successfully.'
        );
    }
}
