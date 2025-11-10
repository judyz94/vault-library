<?php

namespace App\Http\Requests\Books;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
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
            'author_id' => ['required', 'exists:authors,id'],
            'isbn' => ['required', 'string', 'unique:books,isbn'],
            'publication_year' => ['required', 'integer', 'min:1000', 'max:' . date('Y')],
            'available' => ['boolean'],
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
            'title' => [
                'description' => 'Title of the book.',
                'example' => 'The Pragmatic Programmer',
            ],
            'author_id' => [
                'description' => 'ID of the author who wrote the book.',
                'example' => 1,
            ],
            'isbn' => [
                'description' => 'Unique ISBN code of the book.',
                'example' => '978-0135957059',
            ],
            'publication_year' => [
                'description' => 'Year the book was published.',
                'example' => 2019,
            ],
            'available' => [
                'description' => 'Indicates if the book is available for loan.',
                'example' => true,
            ],
        ];
    }
}
