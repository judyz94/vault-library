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

class UserController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $users = User::paginate(10);

        return $this->success(
            UserResource::collection($users),
            'Users retrieved successfully.'
        );
    }

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

    public function show(User $user): JsonResponse
    {
        return $this->success(
            new UserResource($user),
            'User retrieved successfully.'
        );
    }

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

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return $this->success(null, 'User deleted successfully.');
    }
}
