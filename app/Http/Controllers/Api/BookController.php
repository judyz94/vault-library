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

/**
 * @group Book management
 *
 * APIs for managing books
 */
class BookController extends Controller
{
    use ApiResponse;

    /**
     * Get paginated list of books
     *
     * Retrieves a paginated list of books, including their associated author.
     *
     * @apiResource App\Http\Resources\BookResource
     * @apiResourceModel App\Models\Book
     */
    public function index(): JsonResponse
    {
        $books = Book::with('author')->paginate(10);

        return $this->success(
            BookResource::collection($books),
            'Books retrieved successfully.'
        );
    }

    /**
     * Create a new book in the library.
     *
     * Creates a new book record with the given details such as title, author, ISBN,
     * publication year, and availability status.
     *
     * @bodyParam title string required The title of the book. Example: Clean Code
     * @bodyParam author_id int required The ID of the author. Example: 1
     * @bodyParam isbn string required The ISBN of the book (unique). Example: 9780132350884
     * @bodyParam publication_year int required The publication year of the book. Example: 2008
     * @bodyParam available boolean Indicates if the book is available for borrowing. Example: true
     *
     * @apiResource App\Http\Resources\BookResource
     * @apiResourceModel App\Models\Book
     */
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

    /**
     * Show a specific book
     *
     * Retrieves the details of a single book by its ID, including its author.
     *
     * @urlParam id integer required The ID of the book. Example: 1
     *
     * @responseField message string Success message
     * @response 200 {
     * "message": "Book retrieved successfully."
     * }
     */
    public function show(Book $book): JsonResponse
    {
        $book->load('author');

        return $this->success(
            new BookResource($book),
            'Book retrieved successfully.'
        );
    }

    /**
     * Update a book
     *
     * Updates the information of a specific book using validated request data.
     *
     * @urlParam id integer required The ID of the book to update. Example: 1
     * @bodyParam title string The title of the book. Example: "The Silmarillion"
     * @bodyParam author_id integer The ID of the author. Example: 2
     * @bodyParam published_year integer The year the book was published. Example: 1977
     *
     * @apiResource App\Http\Resources\BookResource
     * @apiResourceModel App\Models\Book
     */
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

    /**
     * Delete a book
     *
     * Removes a book record from the database by its ID.
     *
     * @urlParam id integer required The ID of the book to delete. Example: 1
     *
     * @responseField message string Success message
     * @response 200 {
     * "message": "Book deleted successfully."
     * }
     */
    public function destroy(Book $book): JsonResponse
    {
        $book->delete();

        return $this->success(null, 'Book deleted successfully.');
    }

    /**
     *  Search books by title, author name, or ISBN.
     *
     * @queryParam q string required The search query for title, author name, or ISBN. Example: clean
     *
     * @apiResource App\Http\Resources\BookResource
     * @apiResourceModel App\Models\Book
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->query('q');

        if (!$query) {
            return $this->error('Query parameter "q" is required.', 400);
        }

        $books = Book::with('author')
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhereHas('author', function ($q2) use ($query) {
                        $q2->where('name', 'like', "%{$query}%");
                    })
                    ->orWhere('isbn', 'like', "%{$query}%");
            })
            ->paginate(10);

        return $this->success(
            BookResource::collection($books),
            'Books retrieved successfully.'
        );
    }
}
