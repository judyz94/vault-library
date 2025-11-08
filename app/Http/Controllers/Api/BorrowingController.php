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

class BorrowingController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly BorrowingService $borrowingService)
    {
    }

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
