<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Borrows\BookActionRequest;
use App\Http\Resources\BorrowingResource;
use App\Models\User;
use App\Services\BorrowingService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

/**
 * @group Borrowing management
 *
 * APIs for managing book borrowings
 */
class BorrowingController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly BorrowingService $borrowingService)
    {
    }

    /**
     * Borrow a book
     *
     * Allows a user to borrow a specific book if available.
     *
     * @urlParam user_id integer required The ID of the user borrowing the book. Example: 3
     * @bodyParam book_id integer required The ID of the book to borrow. Example: 7
     *
     * @apiResource App\Http\Resources\BorrowingResource
     * @apiResourceModel App\Models\Borrowing
     */
    public function borrow(BookActionRequest $request, User $user): JsonResponse
    {
        try {
            $borrowing = $this->borrowingService->borrow($user, $request->book_id);

            return $this->success(
                new BorrowingResource($borrowing),
                'Book borrowed successfully.',
                201
            );
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Return a borrowed book
     *
     * Allows a user to return a book they previously borrowed.
     *
     * @urlParam user_id integer required The ID of the user returning the book. Example: 3
     * @bodyParam book_id integer required The ID of the borrowed book being returned. Example: 7
     *
     * @apiResource App\Http\Resources\BorrowingResource
     * @apiResourceModel App\Models\Borrowing
     */
    public function return(BookActionRequest $request, User $user): JsonResponse
    {
        try {
            $borrowing = $this->borrowingService->return($user, $request->book_id);

            return $this->success(
                new BorrowingResource($borrowing),
                'Book returned successfully.'
            );
        } catch (ModelNotFoundException $e) {
            return $this->error('No active borrowing found for this book.', 404);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * List all borrowed books for a user
     *
     * Retrieves all active and past borrowed books for a specific user.
     *
     * @urlParam user_id integer required The ID of the user whose borrowings are being retrieved. Example: 3
     *
     * @apiResourceCollection App\Http\Resources\BorrowingResource
     * @apiResourceModel App\Models\Borrowing
     */
    public function userBorrowed(User $user): JsonResponse
    {
        // Only the user owner or admin user can perform the action
        if (auth()->id() !== $user->id && !auth()->user()->isAdmin()) {
            return $this->error('You are not authorized to view this user’s borrowed books.', 403);
        }

        $borrowings = $this->borrowingService->userBorrowed($user);

        return $this->success(
            BorrowingResource::collection($borrowings),
            'Borrowed books retrieved successfully.'
        );
    }
}
