<?php

namespace App\Services\Interfaces;

use App\DTOs\TaskData;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

/**
 * TaskServiceInterface
 * Contract for task business logic layer
 * Defines all task service operations for dependency injection and testing
 */
interface TaskServiceInterface
{
    /**
     * Get list of all tasks with optional filtering
     *
     * @param string|null $status Optional status filter
     * @return Collection Collection of tasks sorted by priority and due_date
     */
    public function listTasks(?string $status = null): Collection;

    /**
     * Create new task
     * Validates uniqueness and sets default status
     *
     * @param TaskData $taskData Validated task data
     * @return Task Created task model
     * @throws \InvalidArgumentException If duplicate title + due_date exists
     */
    public function createTask(TaskData $taskData): Task;

    /**
     * Update task status with transition validation
     *
     * @param int $id Task ID
     * @param string $newStatus Requested new status
     * @return Task Updated task model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If task not found
     * @throws \InvalidArgumentException If status transition is invalid
     */
    public function updateTaskStatus(int $id, string $newStatus): Task;

    /**
     * Delete task if status is 'done'
     *
     * @param int $id Task ID
     * @return bool True if deleted
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If task not found
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException If task not done (403)
     */
    public function deleteTask(int $id): bool;
}