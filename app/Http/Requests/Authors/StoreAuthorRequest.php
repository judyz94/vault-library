<?php

namespace App\Http\Requests\Authors;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuthorRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
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
                'description' => 'The name of the author',
                'example' => 'Robert C. Martin',
            ],
            'author_id' => [
                'description' => 'General information about the book author.',
                'example' => 'American software engineer, known for his book "Clean Code".',
            ],
        ];
    }
}
