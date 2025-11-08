<?php

namespace App\Http\Requests\Books;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:255'],
            'author_id' => ['sometimes', 'exists:authors,id'],
            'isbn' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('books', 'isbn')->ignore($this->book),
            ],
            'publication_year' => ['sometimes', 'integer', 'min:1000', 'max:' . date('Y')],
            'available' => ['sometimes', 'boolean'],
        ];
    }
}
