<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\Author;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => '12345678%',
            'library_id' => 'LIB-0001',
            'role' => UserRoleEnum::Admin->value,
        ]);

        User::factory()->create([
            'name' => 'Library User',
            'email' => 'user@example.com',
            'password' => '12345678%',
            'library_id' => 'LIB-0002',
            'role' => UserRoleEnum::User->value,
        ]);

        Author::factory()->create([
            'name' => 'Robert C. Martin',
            'bio' => 'American software engineer, known for his book "Clean Code".',
        ]);

    }
}
