<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

/**
 * @group User management
 *
 * APIs for managing users
 */
class UserController extends Controller
{
    use ApiResponse;

    /**
     * Get paginated list of users
     *
     * Retrieves a paginated list of users.
     *
     * @apiResource App\Http\Resources\UserResource
     * @apiResourceModel App\Models\User
     */
    public function index(): JsonResponse
    {
        $users = User::paginate(10);

        return $this->success(
            UserResource::collection($users),
            'Users retrieved successfully.'
        );
    }

    /**
     * Create a new user
     *
     * Registers a new user in the system.
     *
     * @bodyParam name string required The name of the user. Example: John Doe
     * @bodyParam email string required The email address of the user. Example: john@example.com
     * @bodyParam password string required The password for the user account. Example: secret123
     * @bodyParam library_id integer required The ID of the library the user belongs to. Example: 1
     * @bodyParam role string The role of the user (admin, user). Default: user. Example: admin
     *
     * @apiResource App\Http\Resources\UserResource
     * @apiResourceModel App\Models\User
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'library_id' => $validated['library_id'],
                'role' => $validated['role'] ?? UserRoleEnum::User->value,
            ]);

            return $this->success(
                new UserResource($user),
                'User created successfully.',
                201
            );
        } catch (QueryException $e) {
            return $this->error('Database error while creating user.', 400, $e->getMessage());
        } catch (Exception $e) {
            return $this->error('Unexpected error while creating user.', 500, $e->getMessage());
        }
    }

    /**
     * Show a specific user
     *
     * Retrieves the details of a specific user by ID.
     *
     * @urlParam id integer required The ID of the user. Example: 1
     *
     * @responseField message string Success message
     * @response 200 {
     *  "message": "User retrieved successfully."
     *  }
     */
    public function show(User $user): JsonResponse
    {
        return $this->success(
            new UserResource($user),
            'User retrieved successfully.'
        );
    }

    /**
     * Update a user
     *
     * Updates an existing user's information.
     *
     * @urlParam id integer required The ID of the user to update. Example: 1
     * @bodyParam name string The name of the user. Example: John Updated
     * @bodyParam email string The email of the user. Example: john.updated@example.com
     * @bodyParam password string The new password (optional). Example: newSecret123
     * @bodyParam library_id integer The ID of the library. Example: 2
     * @bodyParam role string The role of the user (admin, user). Example: admin
     *
     * @apiResource App\Http\Resources\UserResource
     * @apiResourceModel App\Models\User
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        try {
            $validated = $request->validated();

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $user->update($validated);

            return $this->success(
                new UserResource($user),
                'User updated successfully.'
            );
        } catch (QueryException $e) {
            return $this->error('Database error while updating user.', 400, $e->getMessage());
        } catch (Exception $e) {
            return $this->error('Unexpected error while updating user.', 500, $e->getMessage());
        }
    }

    /**
     * Delete a user
     *
     * Deletes a user record from the system.
     *
     * @urlParam id integer required The ID of the user to delete. Example: 1
     *
     * @responseField message string Success message
     * @response 200 {
     *   "message": "User deleted successfully."
     * }
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return $this->success(null, 'User deleted successfully.');
    }
}
