<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * Calls all seeders for the task management system.
     */
    public function run(): void
    {
        # Create test user (optional - for authentication if needed)
        // User::factory(10)->create();
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        # Seed tasks table with sample data
        # This creates 12 tasks with various priorities and statuses
        $this->call(TaskSeeder::class);
    }
}