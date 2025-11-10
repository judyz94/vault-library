<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authors\StoreAuthorRequest;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

/**
 * @group Author management
 *
 * APIs for managing authors
 */
class AuthorController extends Controller
{
    use ApiResponse;

    /**
     * Get paginated list of authors
     *
     * Retrieves a paginated list of authors.
     *
     * @apiResource App\Http\Resources\AuthorResource
     * @apiResourceModel App\Models\Author
     */
    public function index(): JsonResponse
    {
        $authors = Author::paginate(10);

        return $this->success(
            AuthorResource::collection($authors),
            'Authors retrieved successfully.'
        );
    }

    /**
     * Create a new author
     *
     * Registers a new author in the system.
     *
     * @bodyParam name string required The name of the author. Example: John Doe
     * @bodyParam bio string nullable General information about the author. Example: American software engineer.
     *
     * @apiResource App\Http\Resources\AuthorResource
     * @apiResourceModel App\Models\Author
     */
    public function store(StoreAuthorRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $author = Author::create($validated);

            return $this->success(
                new AuthorResource($author),
                'Author created successfully.',
                201
            );
        } catch (QueryException $e) {
            return $this->error('Database error while creating author.', 400, $e->getMessage());
        } catch (Exception $e) {
            return $this->error('Unexpected error while creating author.', 500, $e->getMessage());
        }
    }
}
