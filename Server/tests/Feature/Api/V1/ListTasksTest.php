<?php

namespace Tests\Feature\Api\V1;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ListTasksTest
 * Feature tests for GET /api/v1/tasks endpoint
 * Tests filtering, sorting, and response structure
 */
class ListTasksTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test list all tasks without filter
     * Returns all tasks sorted by priority then due_date
     */
    public function test_can_list_all_tasks(): void
    {
        # Create tasks with different priorities
        Task::factory()->create(['priority' => 'low', 'due_date' => '2026-03-30', 'title' => 'Low Task']);
        Task::factory()->create(['priority' => 'high', 'due_date' => '2026-03-29', 'title' => 'High Task']);
        Task::factory()->create(['priority' => 'medium', 'due_date' => '2026-03-28', 'title' => 'Medium Task']);

        # Send GET request
        $response = $this->getJson('/api/v1/tasks');

        # Assert response status 200
        $response->assertStatus(200);

        # Assert response structure with data wrapper
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'due_date',
                    'priority',
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

        # Assert sorting: high -> medium -> low priority
        $data = $response->json('data');
        $this->assertEquals('high', $data[0]['priority']);
        $this->assertEquals('medium', $data[1]['priority']);
        $this->assertEquals('low', $data[2]['priority']);
    }

    /**
     * Test list returns empty array when no tasks exist
     */
    public function test_list_returns_empty_array_when_no_tasks(): void
    {
        $response = $this->getJson('/api/v1/tasks');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [],
            ]);
    }

    /**
     * Test filter tasks by pending status
     */
    public function test_can_filter_tasks_by_pending_status(): void
    {
        # Create tasks with different statuses
        Task::factory()->create(['status' => 'pending', 'priority' => 'high', 'title' => 'Pending 1']);
        Task::factory()->create(['status' => 'in_progress', 'priority' => 'high', 'title' => 'In Progress']);
        Task::factory()->create(['status' => 'done', 'priority' => 'high', 'title' => 'Done']);
        Task::factory()->create(['status' => 'pending', 'priority' => 'low', 'title' => 'Pending 2']);

        # Send GET request with status filter
        $response = $this->getJson('/api/v1/tasks?status=pending');

        # Assert only pending tasks returned
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(2, $data);
        
        foreach ($data as $task) {
            $this->assertEquals('pending', $task['status']);
        }
    }

    /**
     * Test filter tasks by in_progress status
     */
    public function test_can_filter_tasks_by_in_progress_status(): void
    {
        Task::factory()->create(['status' => 'pending']);
        Task::factory()->create(['status' => 'in_progress', 'title' => 'Progress Task']);

        $response = $this->getJson('/api/v1/tasks?status=in_progress');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('in_progress', $data[0]['status']);
        $this->assertEquals('Progress Task', $data[0]['title']);
    }

    /**
     * Test filter tasks by done status
     */
    public function test_can_filter_tasks_by_done_status(): void
    {
        Task::factory()->create(['status' => 'pending']);
        Task::factory()->create(['status' => 'done', 'title' => 'Completed Task']);

        $response = $this->getJson('/api/v1/tasks?status=done');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('done', $data[0]['status']);
    }

    /**
     * Test invalid status filter returns validation error
     */
    public function test_invalid_status_filter_returns_validation_error(): void
    {
        $response = $this->getJson('/api/v1/tasks?status=invalid_status');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    /**
     * Test sorting by priority then due_date within same priority
     */
    public function test_tasks_sorted_by_priority_then_due_date(): void
    {
        # Create high priority tasks with different due dates
        Task::factory()->create([
            'priority' => 'high',
            'due_date' => '2026-03-30',
            'title' => 'High Later',
        ]);
        Task::factory()->create([
            'priority' => 'high',
            'due_date' => '2026-03-28',
            'title' => 'High Earlier',
        ]);
        Task::factory()->create([
            'priority' => 'high',
            'due_date' => '2026-03-29',
            'title' => 'High Middle',
        ]);

        $response = $this->getJson('/api/v1/tasks');

        $response->assertStatus(200);
        $data = $response->json('data');

        # Assert all are high priority and sorted by due_date ascending
        $this->assertEquals('high', $data[0]['priority']);
        $this->assertEquals('2026-03-28', $data[0]['due_date']); # Earliest first
        $this->assertEquals('2026-03-29', $data[1]['due_date']);
        $this->assertEquals('2026-03-30', $data[2]['due_date']); # Latest last
    }

    /**
     * Test filtered results also maintain priority sorting
     */
    public function test_filtered_results_maintain_priority_sorting(): void
    {
        # Create pending tasks with different priorities
        Task::factory()->create(['status' => 'pending', 'priority' => 'low', 'due_date' => '2026-03-28']);
        Task::factory()->create(['status' => 'pending', 'priority' => 'high', 'due_date' => '2026-03-30']);
        Task::factory()->create(['status' => 'pending', 'priority' => 'medium', 'due_date' => '2026-03-29']);

        $response = $this->getJson('/api/v1/tasks?status=pending');

        $response->assertStatus(200);
        $data = $response->json('data');

        # Assert sorted high -> medium -> low even with filter
        $this->assertEquals('high', $data[0]['priority']);
        $this->assertEquals('medium', $data[1]['priority']);
        $this->assertEquals('low', $data[2]['priority']);
    }
}