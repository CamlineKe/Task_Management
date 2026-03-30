<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Task\StoreTaskRequest;
use App\Http\Requests\Api\V1\Task\UpdateTaskStatusRequest;
use App\Http\Resources\Api\V1\TaskCollection;
use App\Http\Resources\Api\V1\TaskResource;
use App\DTOs\TaskData;
use App\Services\Interfaces\TaskServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * TaskController
 * Handles all task-related API endpoints
 * Delegates business logic to TaskService
 */
class TaskController extends Controller
{
    /**
     * Create controller with service injection
     *
     * @param TaskServiceInterface $taskService Business logic layer
     */
    public function __construct(
        private readonly TaskServiceInterface $taskService,
    ) {
    }

    /**
     * GET /api/v1/tasks
     * List all tasks with optional status filter
     * Sorted by priority (high -> medium -> low) then due_date ascending
     *
     * @param Request $request Query params including optional status filter
     * @return TaskCollection JSON response with tasks array
     */
    public function index(Request $request): TaskCollection
    {
        # Validate status parameter if provided
        $validated = $request->validate([
            'status' => 'sometimes|in:pending,in_progress,done',
        ]);

        # Extract status filter (null if not provided)
        $status = $validated['status'] ?? null;

        # Fetch tasks from service layer
        $tasks = $this->taskService->listTasks($status);

        # Return wrapped collection
        return new TaskCollection($tasks);
    }

    /**
     * POST /api/v1/tasks
     * Create new task
     * Validates uniqueness of title + due_date combination
     *
     * @param StoreTaskRequest $request Validated task data
     * @return JsonResponse Created task with 201 status
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        # Convert validated request to DTO
        $taskData = TaskData::fromArray($request->validated());

        # Create task via service layer
        $task = $this->taskService->createTask($taskData);

        # Return created resource with 201 status
        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * PATCH /api/v1/tasks/{id}/status
     * Update task status with transition validation
     * Enforces: pending -> in_progress -> done only
     *
     * @param UpdateTaskStatusRequest $request Validated status data
     * @param int $id Task ID from URL
     * @return JsonResponse Updated task or error response
     */
    public function updateStatus(UpdateTaskStatusRequest $request, int $id): JsonResponse
    {
        # Get validated status
        $newStatus = $request->validated()['status'];

        try {
            # Attempt status update via service layer
            $task = $this->taskService->updateTaskStatus($id, $newStatus);

            # Return updated task
            return (new TaskResource($task))
                ->response()
                ->setStatusCode(200);

        } catch (\InvalidArgumentException $e) {
            # Invalid transition - return 422 with details
            return response()->json([
                'message' => 'Invalid status transition',
                'current_status' => $this->taskService->listTasks()->firstWhere('id', $id)?->status ?? 'unknown',
                'requested_status' => $newStatus,
            ], 422);
        }
    }

    /**
     * DELETE /api/v1/tasks/{id}
     * Delete task only if status is 'done'
     *
     * @param int $id Task ID from URL
     * @return JsonResponse Empty 204 response or error
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            # Attempt deletion via service layer
            $this->taskService->deleteTask($id);

            # Return 204 No Content on success
            return response()->json(null, 204);

        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            # Task not done - return 403 Forbidden
            return response()->json([
                'message' => 'Only completed tasks can be deleted',
            ], 403);
        }
    }
}