<?php

namespace App\Http\Requests\Users;

use App\Enums\UserRoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'library_id' => ['required', 'string', 'max:255', 'unique:users,library_id'],
            'role' => ['nullable', Rule::enum(UserRoleEnum::class)],
        ];
    }

    /**
     * Define body parameters for API documentation (Scribe).
     *
     * @codeCoverageIgnore
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'The full name of the user.',
                'example' => 'Mariana Perez',
            ],
            'email' => [
                'description' => 'The unique email address of the user.',
                'example' => 'mariana.perez@example.com',
            ],
            'password' => [
                'description' => 'The user’s password. Must be at least 8 characters long and confirmed.',
                'example' => 'password123',
            ],
            'password_confirmation' => [
                'description' => 'Confirmation of the password. Must match the `password` field.',
                'example' => 'password123',
            ],
            'library_id' => [
                'description' => 'The unique library identifier assigned to the user.',
                'example' => 'LIB123456',
            ],
            'role' => [
                'description' => 'The user’s role within the system. Must be one of the values defined in `UserRoleEnum`.',
                'example' => 'admin',
            ],
        ];
    }
}
