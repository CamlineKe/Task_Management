<?php

namespace Tests\Feature\Api\V1;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * CreateTaskTest
 * Feature tests for POST /api/v1/tasks endpoint
 * Tests validation, creation, and error handling
 */
class CreateTaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful task creation with valid data
     * Returns 201 with created task
     */
    public function test_can_create_task_with_valid_data(): void
    {
        # Prepare valid task data
        $data = [
            'title' => 'Complete project proposal',
            'due_date' => '2026-04-01',
            'priority' => 'high',
        ];

        # Send POST request
        $response = $this->postJson('/api/v1/tasks', $data);

        # Assert response status 201 Created
        $response->assertStatus(201);

        # Assert response structure (wrapped in data key by API resource)
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'due_date',
                'priority',
                'status',
                'created_at',
                'updated_at',
            ],
        ]);

        # Assert response values (wrapped in data key by API resource)
        $response->assertJsonFragment([
            'title' => 'Complete project proposal',
            'due_date' => '2026-04-01',
            'priority' => 'high',
            'status' => 'pending', # Default status
        ]);

        # Assert database record exists
        $this->assertDatabaseHas('tasks', [
            'title' => 'Complete project proposal',
            'due_date' => '2026-04-01 00:00:00',
            'priority' => 'high',
            'status' => 'pending',
        ]);
    }

    /**
     * Test task creation with medium priority
     */
    public function test_can_create_task_with_medium_priority(): void
    {
        $data = [
            'title' => 'Update documentation',
            'due_date' => '2026-03-30',
            'priority' => 'medium',
        ];

        $response = $this->postJson('/api/v1/tasks', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'priority' => 'medium',
                'status' => 'pending',
            ]);
    }

    /**
     * Test task creation with low priority
     */
    public function test_can_create_task_with_low_priority(): void
    {
        $data = [
            'title' => 'Refactor old code',
            'due_date' => '2026-03-30',
            'priority' => 'low',
        ];

        $response = $this->postJson('/api/v1/tasks', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'priority' => 'low',
                'status' => 'pending',
            ]);
    }

    /**
     * Test validation fails when title is missing
     * Returns 422 with error message
     */
    public function test_cannot_create_task_without_title(): void
    {
        $data = [
            'due_date' => '2026-04-01',
            'priority' => 'high',
        ];

        $response = $this->postJson('/api/v1/tasks', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /**
     * Test validation fails when due_date is missing
     */
    public function test_cannot_create_task_without_due_date(): void
    {
        $data = [
            'title' => 'Test Task',
            'priority' => 'high',
        ];

        $response = $this->postJson('/api/v1/tasks', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['due_date']);
    }

    /**
     * Test validation fails when priority is missing
     */
    public function test_cannot_create_task_without_priority(): void
    {
        $data = [
            'title' => 'Test Task',
            'due_date' => '2026-03-29',
        ];

        $response = $this->postJson('/api/v1/tasks', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['priority']);
    }

    /**
     * Test validation fails for invalid priority value
     */
    public function test_cannot_create_task_with_invalid_priority(): void
    {
        $data = [
            'title' => 'Test Task',
            'due_date' => '2026-04-01',
            'priority' => 'urgent', # Invalid priority
        ];

        $response = $this->postJson('/api/v1/tasks', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['priority']);
    }

    /**
     * Test validation fails for past due date
     */
    public function test_cannot_create_task_with_past_due_date(): void
    {
        $data = [
            'title' => 'Test Task',
            'due_date' => '2020-01-01', # Past date
            'priority' => 'high',
        ];

        $response = $this->postJson('/api/v1/tasks', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['due_date'])
            ->assertJsonFragment([
                'message' => 'The due date must be today or a future date.',
            ]);
    }

    /**
     * Test validation fails for invalid date format
     */
    public function test_cannot_create_task_with_invalid_date_format(): void
    {
        $data = [
            'title' => 'Test Task',
            'due_date' => '29-03-2026', # Wrong format (DD-MM-YYYY)
            'priority' => 'high',
        ];

        $response = $this->postJson('/api/v1/tasks', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['due_date']);
    }

    /**
     * Test cannot create duplicate task with same title and due_date
     * Database unique constraint should prevent this
     */
    public function test_cannot_create_duplicate_task(): void
    {
        # Create existing task
        Task::factory()->create([
            'title' => 'Duplicate Task',
            'due_date' => '2026-03-29',
            'priority' => 'high',
        ]);

        # Attempt to create duplicate
        $data = [
            'title' => 'Duplicate Task',
            'due_date' => '2026-03-29',
            'priority' => 'medium', # Different priority doesn't matter
        ];

        $response = $this->postJson('/api/v1/tasks', $data);

        # Should return 422 due to validation or database error
        $response->assertStatus(422);
    }

    /**
     * Test can create task with same title but different due_date
     */
    public function test_can_create_task_with_same_title_different_date(): void
    {
        # Create existing task
        Task::factory()->create([
            'title' => 'Same Title',
            'due_date' => '2026-03-29',
        ]);

        # Create new task with same title but different date
        $data = [
            'title' => 'Same Title',
            'due_date' => '2026-03-30', # Different date
            'priority' => 'high',
        ];

        $response = $this->postJson('/api/v1/tasks', $data);

        $response->assertStatus(201);

        # Assert two records exist
        $this->assertDatabaseCount('tasks', 2);
    }
}