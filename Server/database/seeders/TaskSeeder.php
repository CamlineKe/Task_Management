<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * TaskSeeder - Seeds the tasks table with sample data
 * Includes high, medium, and low priority tasks with various statuses
 */
class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds
     * Inserts 12 sample tasks with realistic data
     */
    public function run(): void
    {
        DB::table('tasks')->insert([
            # HIGH PRIORITY TASKS (3 tasks)
            [
                'title' => 'Complete project proposal',
                'due_date' => '2026-03-29',
                'priority' => 'high',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Review pull requests',
                'due_date' => '2026-03-28',
                'priority' => 'high',
                'status' => 'in_progress',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Deploy to production',
                'due_date' => '2026-03-30',
                'priority' => 'high',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            # MEDIUM PRIORITY TASKS (5 tasks)
            [
                'title' => 'Update documentation',
                'due_date' => '2026-03-28',
                'priority' => 'medium',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Write unit tests',
                'due_date' => '2026-03-29',
                'priority' => 'medium',
                'status' => 'in_progress',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Optimize database queries',
                'due_date' => '2026-03-27',
                'priority' => 'medium',
                'status' => 'done',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Code review feedback',
                'due_date' => '2026-03-28',
                'priority' => 'medium',
                'status' => 'done',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Fix UI responsiveness',
                'due_date' => '2026-03-30',
                'priority' => 'medium',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            # LOW PRIORITY TASKS (4 tasks)
            [
                'title' => 'Refactor old code',
                'due_date' => '2026-03-28',
                'priority' => 'low',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Update README',
                'due_date' => '2026-03-29',
                'priority' => 'low',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Clean up console logs',
                'due_date' => '2026-03-27',
                'priority' => 'low',
                'status' => 'done',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}