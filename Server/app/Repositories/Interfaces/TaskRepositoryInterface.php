<?php

namespace App\Repositories\Interfaces;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

/**
 * TaskRepositoryInterface
 * Contract for task data access layer
 * Defines all task repository operations for dependency injection and testing
 */
interface TaskRepositoryInterface
{
    /**
     * Get all tasks with optional status filter and sorting
     *
     * @param string|null $status Filter by status (pending, in_progress, done) or null for all
     * @return Collection Collection of Task models sorted by priority then due_date
     */
    public function getAll(?string $status = null): Collection;

    /**
     * Find task by ID
     *
     * @param int $id Task ID
     * @return Task|null Task model or null if not found
     */
    public function findById(int $id): ?Task;

    /**
     * Find task by ID or fail
     *
     * @param int $id Task ID
     * @return Task Task model or throws ModelNotFoundException
     */
    public function findOrFail(int $id): Task;

    /**
     * Check if task with title and due_date already exists
     *
     * @param string $title Task title
     * @param string $dueDate Due date in Y-m-d format
     * @param int|null $excludeId Optional ID to exclude from check (for updates)
     * @return bool True if duplicate exists, false otherwise
     */
    public function existsByTitleAndDueDate(string $title, string $dueDate, ?int $excludeId = null): bool;

    /**
     * Create new task
     *
     * @param array $data Task data array with title, due_date, priority, status
     * @return Task Created task model
     */
    public function create(array $data): Task;

    /**
     * Update task status
     *
     * @param Task $task Task model to update
     * @param string $status New status value
     * @return bool True if updated successfully
     */
    public function updateStatus(Task $task, string $status): bool;

    /**
     * Delete task
     *
     * @param Task $task Task model to delete
     * @return bool True if deleted successfully
     */
    public function delete(Task $task): bool;

    /**
     * Get daily report aggregation by priority and status
     *
     * @param string $date Date in Y-m-d format
     * @return Collection Collection of aggregated rows with priority, status, total
     */
    public function getDailyReport(string $date): Collection;
}