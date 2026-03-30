<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating tasks table
 * Includes all indexes and constraints as per specification
 */
return new class extends Migration
{
    /**
     * Run the migrations
     * Creates tasks table with optimized indexes for performance
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            # Primary key - auto-incrementing ID
            $table->id();

            # Task title - string for task name
            $table->string('title');

            # Due date - date only (no time component)
            $table->date('due_date');

            # Priority enum - low, medium, high
            $table->enum('priority', ['low', 'medium', 'high']);

            # Status enum - pending, in_progress, done with default pending
            $table->enum('status', ['pending', 'in_progress', 'done'])->default('pending');

            # Laravel timestamps - created_at and updated_at
            $table->timestamps();

            # UNIQUE CONSTRAINT: prevents duplicate title + due_date combination
            # Also serves as index for uniqueness checks and prevents race conditions
            $table->unique(['title', 'due_date'], 'unique_title_due_date');

            # INDEX: due_date filtering for validation and daily report
            $table->index('due_date', 'idx_tasks_due_date');

            # INDEX: status filtering in list endpoint
            $table->index('status', 'idx_tasks_status');

            # INDEX: priority sorting
            $table->index('priority', 'idx_tasks_priority');

            # COMPOSITE INDEX: list with status filter + sorting
            # Covers: WHERE status = ? ORDER BY priority, due_date
            $table->index(['status', 'priority', 'due_date'], 'idx_tasks_status_priority_due_date');

            # COMPOSITE INDEX: list without status filter + sorting
            # Covers: ORDER BY priority, due_date
            $table->index(['priority', 'due_date'], 'idx_tasks_priority_due_date');

            # COMPOSITE INDEX: daily report aggregation
            # Covers: WHERE due_date = ? GROUP BY priority, status
            # Enables index-only scan for report query
            $table->index(['due_date', 'priority', 'status'], 'idx_tasks_due_date_priority_status');
        });
    }

    /**
     * Reverse the migrations
     * Drops the tasks table completely
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};