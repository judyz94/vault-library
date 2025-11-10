<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author_id',
        'isbn',
        'publication_year',
        'available',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'borrowings')
            ->withPivot(['borrowed_at', 'due_at', 'returned_at'])
            ->withTimestamps();
    }
}
