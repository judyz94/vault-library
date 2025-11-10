<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginUserRequest extends FormRequest
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
            'email' => 'required|email',
            'password' => 'required',
        ];
    }

    /**
     * Define body parameters for API documentation (Scribe).
     *
     * @return array<string, array<string, mixed>>
     * @codeCoverageIgnore
     */
    public function bodyParameters(): array
    {
        return [
            'email' => [
                'description' => 'The email address of the user.',
                'example' => 'johndoe@example.com',
            ],
            'password' => [
                'description' => 'The password of the user.',
                'example' => 'secret123',
            ],
        ];
    }
}
