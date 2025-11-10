<?php

namespace Tests\Feature\Api;

use App\Enums\UserRoleEnum;
use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $libraryUser;
    protected User $adminUser;
    protected Author $author;
    protected Book $book;

    protected function setUp(): void
    {
        parent::setUp();

        $this->libraryUser = User::factory()->create([
            'role' => UserRoleEnum::User->value,
        ]);

        $this->adminUser = User::factory()->create([
            'role' => UserRoleEnum::Admin->value,
        ]);

        $this->author = Author::factory()->create();

        $this->book = Book::factory()->create([
            'author_id' => $this->author->id,
        ]);
    }

    public function test_index_returns_paginated_books(): void
    {
        Book::factory()->count(15)->create(['author_id' => $this->author->id]);

        $response = $this->actingAs($this->libraryUser, 'sanctum')
            ->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Books retrieved successfully.',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data'
            ]);
    }

    public function test_show_returns_single_book(): void
    {
        $response = $this->actingAs($this->libraryUser, 'sanctum')
            ->getJson("/api/books/{$this->book->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Book retrieved successfully.'
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['id', 'title', 'isbn', 'publication_year', 'available', 'author']
            ]);
    }

    public function test_store_creates_book_successfully_as_admin(): void
    {
        $payload = [
            'title' => 'Clean Code',
            'author_id' => $this->author->id,
            'isbn' => '9780132350884',
            'publication_year' => 2008,
            'available' => true,
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/books', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Book created successfully.'
            ]);

        $this->assertDatabaseHas('books', [
            'title' => 'Clean Code',
            'isbn' => '9780132350884'
        ]);
    }

    public function test_store_fails_for_non_admin(): void
    {
        $payload = [
            'title' => 'Unauthorized Book',
            'author_id' => $this->author->id,
            'isbn' => '9780132350999',
            'publication_year' => 2010,
            'available' => true,
        ];

        $response = $this->actingAs($this->libraryUser, 'sanctum')
            ->postJson('/api/books', $payload);

        $response->assertStatus(403);
    }

    public function test_update_book_successfully_as_admin(): void
    {
        $payload = [
            'title' => 'Updated Title',
            'isbn' => '9780132350000',
            'publication_year' => 2024,
            'available' => false
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/books/{$this->book->id}", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Book updated successfully.'
            ]);

        $this->assertDatabaseHas('books', [
            'id' => $this->book->id,
            'title' => 'Updated Title',
            'isbn' => '9780132350000',
            'available' => false
        ]);
    }

    public function test_update_fails_for_non_admin(): void
    {
        $payload = ['title' => 'Unauthorized Update'];

        $response = $this->actingAs($this->libraryUser, 'sanctum')
            ->putJson("/api/books/{$this->book->id}", $payload);

        $response->assertStatus(403);
    }

    public function test_destroy_deletes_book_as_admin(): void
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/books/{$this->book->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Book deleted successfully.'
            ]);

        $this->assertDatabaseMissing('books', ['id' => $this->book->id]);
    }

    public function test_destroy_fails_for_non_admin(): void
    {
        $response = $this->actingAs($this->libraryUser, 'sanctum')
            ->deleteJson("/api/books/{$this->book->id}");

        $response->assertStatus(403);
    }

    public function test_search_returns_books_matching_query(): void
    {
        Book::factory()->create(['title' => 'Clean Code', 'author_id' => $this->author->id]);
        Book::factory()->create(['title' => 'Code Complete', 'author_id' => $this->author->id]);

        $response = $this->actingAs($this->libraryUser, 'sanctum')
            ->getJson('/api/books/search?q=Code');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Books retrieved successfully.'
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data'
            ]);
    }

    public function test_search_returns_error_when_query_missing(): void
    {
        $response = $this->actingAs($this->libraryUser, 'sanctum')
            ->getJson('/api/books/search');

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'message' => 'Query parameter "q" is required.'
            ]);
    }
}
