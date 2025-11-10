<?php

namespace Tests\Feature\Api;

use App\Enums\UserRoleEnum;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $adminUser;
    protected Book $book;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => UserRoleEnum::User->value,
        ]);

        $this->adminUser = User::factory()->create([
            'role' => UserRoleEnum::Admin->value,
        ]);

        $this->book = Book::factory()->create([
            'available' => true,
        ]);
    }

    public function test_user_can_borrow_an_available_book(): void
    {
        $payload = ['book_id' => $this->book->id];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/users/{$this->user->id}/borrow", $payload);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Book borrowed successfully.',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['id', 'user_id', 'book_id', 'borrowed_at', 'returned_at']
            ]);

        $this->assertEquals(false, $this->book->fresh()->available);

        $borrowing = Borrowing::where('user_id', $this->user->id)
            ->where('book_id', $this->book->id)
            ->first();

        $this->assertDatabaseHas('borrowings', [
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
        ]);

        $this->assertNotNull($borrowing->borrowed_at);
        $this->assertNotNull($borrowing->due_at);}

    public function test_borrow_fails_if_book_not_available(): void
    {
        $this->book->update(['available' => false]);

        $payload = ['book_id' => $this->book->id];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/users/{$this->user->id}/borrow", $payload);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'message' => 'This book is currently unavailable.',
            ]);
    }

    public function test_borrow_fails_if_user_has_3_active_borrowings(): void
    {
        $user = User::factory()->create();
        $books = Book::factory()->count(3)->create(['available' => false]);

        foreach ($books as $book) {
            Borrowing::factory()->create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'returned_at' => null,
            ]);
        }

        $newBook = Book::factory()->create(['available' => true]);
        $payload = ['book_id' => $newBook->id];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/users/{$user->id}/borrow", $payload);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'message' => 'User already has 3 borrowed books.',
            ]);
    }

    public function test_user_can_create_borrowing_with_due_date_14_days_later(): void
    {
        Carbon::setTestNow('2025-11-10 10:00:00');

        $payload = ['book_id' => $this->book->id];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/users/{$this->user->id}/borrow", $payload);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Book borrowed successfully.',
            ]);

        $borrowing = Borrowing::where('user_id', $this->user->id)
            ->where('book_id', $this->book->id)
            ->first();

        $this->assertEquals(
            Carbon::now()->addDays(14)->format('Y-m-d'),
            $borrowing->due_at->format('Y-m-d')
        );
    }

    public function test_user_can_return_a_book_successfully(): void
    {
        $borrowing = Borrowing::factory()->create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'returned_at' => null,
        ]);

        $payload = ['book_id' => $this->book->id];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/users/{$this->user->id}/return", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Book returned successfully.',
            ]);

        $this->assertDatabaseHas('borrowings', [
            'id' => $borrowing->id,
        ]);
        $this->assertEquals(true, $this->book->fresh()->available);
        $this->assertNotNull($borrowing->fresh()->returned_at);
    }

    public function test_return_fails_if_no_active_borrowing_found(): void
    {
        $payload = ['book_id' => $this->book->id];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/users/{$this->user->id}/return", $payload);

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'No active borrowing found for this book.',
            ]);
    }

    public function test_user_can_view_their_own_borrowed_books(): void
    {
        Borrowing::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'returned_at' => null,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/users/{$this->user->id}/borrowed");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Borrowed books retrieved successfully.',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [['id', 'book_id', 'user_id', 'borrowed_at', 'returned_at']]
            ]);
    }

    public function test_admin_can_view_any_user_borrowed_books(): void
    {
        Borrowing::factory()->count(2)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/users/{$this->user->id}/borrowed");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Borrowed books retrieved successfully.',
            ]);
    }

    public function test_user_cannot_view_another_user_borrowed_books(): void
    {
        $otherUser = User::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/users/{$otherUser->id}/borrowed");

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'You are not authorized to view this user’s borrowed books.',
            ]);
    }
}
