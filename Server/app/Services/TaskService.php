<?php

namespace App\Services;

use App\DTOs\TaskData;
use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Services\Interfaces\TaskServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * TaskService
 * Concrete implementation of task business logic
 * Handles validation, status transitions, and business rules
 */
class TaskService implements TaskServiceInterface
{
    /**
     * Create service instance with repository injection
     *
     * @param TaskRepositoryInterface $taskRepository Task data access layer
     */
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
    ) {
    }

    /**
     * List tasks with optional status filter
     * Returns tasks sorted by priority (high -> medium -> low) then due_date
     *
     * @param string|null $status Optional status filter
     * @return Collection Sorted task collection
     */
    public function listTasks(?string $status = null): Collection
    {
        return $this->taskRepository->getAll($status);
    }

    /**
     * Create new task with uniqueness validation
     * Uses database transaction for race condition protection
     *
     * @param TaskData $taskData Validated task data
     * @return Task Created task
     * @throws \InvalidArgumentException If duplicate exists
     */
    public function createTask(TaskData $taskData): Task
    {
        # Check for duplicate title + due_date combination
        # This check + database unique constraint provides double protection
        $exists = $this->taskRepository->existsByTitleAndDueDate(
            $taskData->title,
            $taskData->dueDate
        );

        if ($exists) {
            throw new \InvalidArgumentException('A task with this title and due date already exists.');
        }

        # Create task - status defaults to 'pending' via model
        return $this->taskRepository->create($taskData->toArray());
    }

    /**
     * Update task status with strict transition validation
     * Enforces: pending -> in_progress -> only
     *
     * @param int $id Task ID
     * @param string $newStatus Requested status
     * @return Task Updated task
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \InvalidArgumentException If transition invalid
     */
    public function updateTaskStatus(int $id, string $newStatus): Task
    {
        # Find task or fail - 404 if not found
        $task = $this->taskRepository->findOrFail($id);

        # Validate status transition using model method
        # Business rule: pending -> in_progress -> done only, no skipping, no reverting
        if (!$task->canTransitionTo($newStatus)) {
            throw new \InvalidArgumentException(
                "Invalid status transition from '{$task->status}' to '{$newStatus}'"
            );
        }

        # Update status
        $this->taskRepository->updateStatus($task, $newStatus);

        # Refresh model to get updated data
        $task->refresh();

        return $task;
    }

    /**
     * Delete task only if status is 'done'
     *
     * @param int $id Task ID
     * @return bool True if deleted
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws HttpException 403 if task not done
     */
    public function deleteTask(int $id): bool
    {
        # Find task or fail - 404 if not found
        $task = $this->taskRepository->findOrFail($id);

        # Business rule: only completed tasks can be deleted
        if ($task->status !== 'done') {
            throw new HttpException(403, 'Only completed tasks can be deleted.');
        }

        return $this->taskRepository->delete($task);
    }
}