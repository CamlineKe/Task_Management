<?php

namespace Tests\Unit\Services;

use App\DTOs\TaskData;
use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Services\TaskService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * TaskServiceTest
 * Unit tests for TaskService business logic
 * Tests task creation, status updates, and deletion rules
 */
class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Mock repository instance
     */
    private TaskRepositoryInterface $repository;

    /**
     * Service instance under test
     */
    private TaskService $service;

    /**
     * Set up test environment before each test
     */
    protected function setUp(): void
    {
        parent::setUp();

        # Create mock repository
        $this->repository = $this->createMock(TaskRepositoryInterface::class);

        # Create service with mocked repository
        $this->service = new TaskService($this->repository);
    }

    /**
     * Test createTask succeeds when no duplicate exists
     */
    public function test_create_task_succeeds_when_no_duplicate(): void
    {
        # Create task data DTO
        $taskData = new TaskData(
            title: 'New Task',
            dueDate: '2026-03-29',
            priority: 'high'
        );

        # Configure mock - no duplicate exists
        $this->repository
            ->expects($this->once())
            ->method('existsByTitleAndDueDate')
            ->with('New Task', '2026-03-29')
            ->willReturn(false);

        # Configure mock - create returns new task
        $createdTask = new Task([
            'id' => 1,
            'title' => 'New Task',
            'due_date' => '2026-03-29',
            'priority' => 'high',
            'status' => 'pending',
        ]);

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with($taskData->toArray())
            ->willReturn($createdTask);

        # Execute service method
        $result = $this->service->createTask($taskData);

        # Assert result
        $this->assertEquals('New Task', $result->title);
        $this->assertEquals('pending', $result->status);
    }

    /**
     * Test createTask throws exception when duplicate exists
     */
    public function test_create_task_throws_exception_on_duplicate(): void
    {
        # Create task data DTO with duplicate data
        $taskData = new TaskData(
            title: 'Existing Task',
            dueDate: '2026-03-29',
            priority: 'high'
        );

        # Configure mock - duplicate exists
        $this->repository
            ->expects($this->once())
            ->method('existsByTitleAndDueDate')
            ->with('Existing Task', '2026-03-29')
            ->willReturn(true);

        # Assert exception thrown
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('A task with this title and due date already exists.');

        # Execute service method - should throw
        $this->service->createTask($taskData);
    }

    /**
     * Test updateTaskStatus succeeds for valid transition
     */
    public function test_update_status_succeeds_for_valid_transition(): void
    {
        # Create task in database so refresh() works properly
        $task = Task::factory()->create([
            'title' => 'Test Task',
            'due_date' => '2026-04-01',
            'priority' => 'high',
            'status' => 'pending',
        ]);

        $taskId = $task->id;

        # Configure mock - find task (return fresh instance from DB)
        $this->repository
            ->expects($this->once())
            ->method('findOrFail')
            ->with($taskId)
            ->willReturnCallback(function ($id) {
                return Task::find($id);
            });

        # Configure mock - update status (update DB record)
        $this->repository
            ->expects($this->once())
            ->method('updateStatus')
            ->willReturnCallback(function ($task, $status) {
                $task->update(['status' => $status]);
                return true;
            });

        # Execute service method
        $result = $this->service->updateTaskStatus($taskId, 'in_progress');

        # Assert result
        $this->assertEquals($taskId, $result->id);
        $this->assertEquals('in_progress', $result->status);
    }

    /**
     * Test updateTaskStatus throws exception for invalid transition
     */
    public function test_update_status_throws_exception_for_invalid_transition(): void
    {
        # Create task with pending status
        $task = new Task([
            'id' => 1,
            'status' => 'pending',
        ]);

        # Configure mock - find task
        $this->repository
            ->expects($this->once())
            ->method('findOrFail')
            ->with(1)
            ->willReturn($task);

        # Assert exception thrown for invalid transition (pending -> done)
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid status transition from 'pending' to 'done'");

        # Execute service method - should throw
        $this->service->updateTaskStatus(1, 'done');
    }

    /**
     * Test deleteTask succeeds when task is done
     */
    public function test_delete_task_succeeds_when_done(): void
    {
        # Create task with done status
        $task = new Task([
            'id' => 1,
            'status' => 'done',
        ]);

        # Configure mock - find task
        $this->repository
            ->expects($this->once())
            ->method('findOrFail')
            ->with(1)
            ->willReturn($task);

        # Configure mock - delete returns true
        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->with($task)
            ->willReturn(true);

        # Execute service method
        $result = $this->service->deleteTask(1);

        # Assert result
        $this->assertTrue($result);
    }

    /**
     * Test deleteTask throws 403 when task is pending
     */
    public function test_delete_task_throws_403_when_pending(): void
    {
        # Create task with pending status
        $task = new Task([
            'id' => 1,
            'status' => 'pending',
        ]);

        # Configure mock - find task
        $this->repository
            ->expects($this->once())
            ->method('findOrFail')
            ->with(1)
            ->willReturn($task);

        # Assert exception thrown
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Only completed tasks can be deleted.');

        # Execute service method - should throw 403
        $this->service->deleteTask(1);
    }

    /**
     * Test deleteTask throws 403 when task is in_progress
     */
    public function test_delete_task_throws_403_when_in_progress(): void
    {
        # Create task with in_progress status
        $task = new Task([
            'id' => 1,
            'status' => 'in_progress',
        ]);

        # Configure mock - find task
        $this->repository
            ->expects($this->once())
            ->method('findOrFail')
            ->with(1)
            ->willReturn($task);

        # Assert exception thrown
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Only completed tasks can be deleted.');

        # Execute service method - should throw 403
        $this->service->deleteTask(1);
    }

    /**
     * Test listTasks returns all tasks when no status filter
     */
    public function test_list_tasks_returns_all_without_filter(): void
    {
        # Create mock tasks
        $tasks = [
            new Task(['id' => 1, 'status' => 'pending']),
            new Task(['id' => 2, 'status' => 'done']),
        ];

        # Configure mock - get all with null filter
        $this->repository
            ->expects($this->once())
            ->method('getAll')
            ->with(null)
            ->willReturn(new \Illuminate\Database\Eloquent\Collection($tasks));

        # Execute service method
        $result = $this->service->listTasks();

        # Assert result count
        $this->assertCount(2, $result);
    }

    /**
     * Test listTasks returns filtered tasks when status provided
     */
    public function test_list_tasks_returns_filtered_with_status(): void
    {
        # Create mock pending tasks
        $tasks = [
            new Task(['id' => 1, 'status' => 'pending']),
            new Task(['id' => 3, 'status' => 'pending']),
        ];

        # Configure mock - get all with pending filter
        $this->repository
            ->expects($this->once())
            ->method('getAll')
            ->with('pending')
            ->willReturn(new \Illuminate\Database\Eloquent\Collection($tasks));

        # Execute service method
        $result = $this->service->listTasks('pending');

        # Assert result count
        $this->assertCount(2, $result);
    }
}