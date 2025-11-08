<?php

namespace App\Http\Requests\Borrows;

use Illuminate\Foundation\Http\FormRequest;

class BookActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only the borrow owner or admin user can perform the action
        $authUser = auth()->user();
        $routeUser = $this->route('user');

        return $authUser && ($authUser->id === (int) $routeUser->id || $authUser->isAdmin());
    }

    public function rules(): array
    {
        return [
            'book_id' => ['required', 'exists:books,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'book_id.required' => 'A book ID is required.',
            'book_id.exists' => 'The selected book does not exist.',
        ];
    }
}
