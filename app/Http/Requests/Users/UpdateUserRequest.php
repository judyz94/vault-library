<?php

namespace App\Http\Requests\Users;

use App\Enums\UserRoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user')?->id;

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'library_id' => ['required', 'string', 'max:255', Rule::unique('users', 'library_id')->ignore($userId)],
            'role' => ['sometimes', Rule::enum(UserRoleEnum::class)],
        ];
    }

    /**
     * Define body parameters for API documentation (Scribe).
     *
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'The updated full name of the user. Only required if you want to change it.',
                'example' => 'Laura Gómez',
            ],
            'email' => [
                'description' => 'The updated email address of the user. Must be unique in the system.',
                'example' => 'laura.gomez@example.com',
            ],
            'password' => [
                'description' => 'The new password for the user. Must be confirmed and at least 8 characters long.',
                'example' => 'newpassword123',
            ],
            'password_confirmation' => [
                'description' => 'Confirmation of the new password. Must match the `password` field.',
                'example' => 'newpassword123',
            ],
            'library_id' => [
                'description' => 'The unique library identifier assigned to the user. Required and must be unique.',
                'example' => 'LIB789123',
            ],
            'role' => [
                'description' => 'The user’s role within the system. Optional. Must be one of the values defined in `UserRoleEnum`.',
                'example' => 'librarian',
            ],
        ];
    }
}
