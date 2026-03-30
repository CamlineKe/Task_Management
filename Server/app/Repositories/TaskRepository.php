<?php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * TaskRepository
 * Concrete implementation of task data access layer
 * Handles all database operations for tasks with optimized queries
 */
class TaskRepository implements TaskRepositoryInterface
{
    /**
     * Get all tasks with optional status filter
     * Sorted by priority (high -> medium -> low) then due_date ascending
     *
     * @param string|null $status Optional status filter
     * @return Collection Sorted collection of tasks
     */
    public function getAll(?string $status = null): Collection
    {
        $query = Task::query();

        # Apply status filter if provided - uses idx_tasks_status index
        if ($status !== null) {
            $query->where('status', $status);
        }

        # Sort by priority using CASE for cross-database compatibility
        # Then by due_date ascending - uses idx_tasks_priority_due_date or idx_tasks_status_priority_due_date
        return $query
            ->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 END")
            ->orderBy('due_date', 'asc')
            ->get();
    }

    /**
     * Find task by ID
     *
     * @param int $id Task ID
     * @return Task|null Null if not found
     */
    public function findById(int $id): ?Task
    {
        return Task::find($id);
    }

    /**
     * Find task by ID or throw exception
     *
     * @param int $id Task ID
     * @return Task Task model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): Task
    {
        return Task::findOrFail($id);
    }

    /**
     * Check for duplicate title + due_date combination
     * Uses unique_title_due_date index for fast lookup
     *
     * @param string $title Task title to check
     * @param string $dueDate Due date to check
     * @param int|null $excludeId Optional ID to exclude (for update scenarios)
     * @return bool True if exists, false otherwise
     */
    public function existsByTitleAndDueDate(string $title, string $dueDate, ?int $excludeId = null): bool
    {
        $query = Task::where('title', $title)
            ->where('due_date', $dueDate);

        # Exclude specific ID if provided (useful for updates)
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Create new task in database
     *
     * @param array $data Task attributes
     * @return Task Created task with ID
     */
    public function create(array $data): Task
    {
        return Task::create($data);
    }

    /**
     * Update task status
     *
     * @param Task $task Task to update
     * @param string $status New status
     * @return bool Success indicator
     */
    public function updateStatus(Task $task, string $status): bool
    {
        return $task->update(['status' => $status]);
    }

    /**
     * Delete task from database
     *
     * @param Task $task Task to delete
     * @return bool Success indicator
     */
    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    /**
     * Get daily report aggregation
     * Uses idx_tasks_due_date_priority_status for index-only scan
     *
     * @param string $date Date in Y-m-d format
     * @return Collection Collection with priority, status, total count
     */
    public function getDailyReport(string $date): Collection
    {
        return Task::whereDate('due_date', $date)
            ->select('priority', 'status', DB::raw('count(*) as total'))
            ->groupBy('priority', 'status')
            ->get();
    }
}