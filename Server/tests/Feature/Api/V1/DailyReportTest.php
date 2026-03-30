<?php

namespace Tests\Feature\Api\V1;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * DailyReportTest
 * Feature tests for GET /api/v1/tasks/report endpoint
 * Tests date validation, aggregation, and report structure
 */
class DailyReportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful report generation for date with tasks
     * Returns structured summary with counts
     */
    public function test_can_get_daily_report_with_tasks(): void
    {
        # Create tasks for specific date with various priorities and statuses
        Task::factory()->create([
            'title' => 'High Pending 1',
            'due_date' => '2026-04-01',
            'priority' => 'high',
            'status' => 'pending',
        ]);
        Task::factory()->create([
            'title' => 'High Pending 2',
            'due_date' => '2026-04-01',
            'priority' => 'high',
            'status' => 'pending',
        ]);
        Task::factory()->create([
            'title' => 'High In Progress',
            'due_date' => '2026-04-01',
            'priority' => 'high',
            'status' => 'in_progress',
        ]);
        Task::factory()->create([
            'title' => 'Medium Done',
            'due_date' => '2026-04-01',
            'priority' => 'medium',
            'status' => 'done',
        ]);
        Task::factory()->create([
            'title' => 'Low Done',
            'due_date' => '2026-04-01',
            'priority' => 'low',
            'status' => 'done',
        ]);

        # Send GET request with date parameter
        $response = $this->getJson('/api/v1/tasks/report?date=2026-04-01');

        # Assert response status 200
        $response->assertStatus(200);

        # Assert response structure
        $response->assertJsonStructure([
            'date',
            'summary' => [
                'high' => ['pending', 'in_progress', 'done'],
                'medium' => ['pending', 'in_progress', 'done'],
                'low' => ['pending', 'in_progress', 'done'],
            ],
        ]);

        # Assert response values
        $response->assertJson([
            'date' => '2026-04-01',
            'summary' => [
                'high' => [
                    'pending' => 2,
                    'in_progress' => 1,
                    'done' => 0,
                ],
                'medium' => [
                    'pending' => 0,
                    'in_progress' => 0,
                    'done' => 1,
                ],
                'low' => [
                    'pending' => 0,
                    'in_progress' => 0,
                    'done' => 1,
                ],
            ],
        ]);
    }

    /**
     * Test report returns zeros for date with no tasks
     */
    public function test_report_returns_zeros_for_empty_date(): void
    {
        # Request report for date with no tasks
        $response = $this->getJson('/api/v1/tasks/report?date=2026-04-01');

        $response->assertStatus(200)
            ->assertJson([
                'date' => '2026-04-01',
                'summary' => [
                    'high' => ['pending' => 0, 'in_progress' => 0, 'done' => 0],
                    'medium' => ['pending' => 0, 'in_progress' => 0, 'done' => 0],
                    'low' => ['pending' => 0, 'in_progress' => 0, 'done' => 0],
                ],
            ]);
    }

    /**
     * Test report excludes tasks with different due_date
     */
    public function test_report_excludes_tasks_with_different_date(): void
    {
        # Create task for different date
        Task::factory()->create([
            'due_date' => '2026-04-02',
            'priority' => 'high',
            'status' => 'pending',
        ]);

        # Request report for April 1
        $response = $this->getJson('/api/v1/tasks/report?date=2026-04-01');

        # Assert task from March 28 is not counted
        $response->assertStatus(200)
            ->assertJson([
                'summary' => [
                    'high' => ['pending' => 0, 'in_progress' => 0, 'done' => 0],
                ],
            ]);
    }

    /**
     * Test validation fails when date parameter is missing
     */
    public function test_report_requires_date_parameter(): void
    {
        $response = $this->getJson('/api/v1/tasks/report');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date'])
            ->assertJsonFragment([
                'message' => 'The date field is required.',
            ]);
    }

    /**
     * Test validation fails for invalid date format
     */
    public function test_report_rejects_invalid_date_format(): void
    {
        $response = $this->getJson('/api/v1/tasks/report?date=29-03-2026');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date'])
            ->assertJsonFragment([
                'message' => 'The date does not match the format YYYY-MM-DD.',
            ]);
    }

    /**
     * Test validation fails for invalid calendar date
     */
    public function test_report_rejects_invalid_calendar_date(): void
    {
        # February 30 does not exist
        $response = $this->getJson('/api/v1/tasks/report?date=2026-02-30');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date']);
    }

    /**
     * Test report with all priority and status combinations
     */
    public function test_report_with_all_combinations(): void
    {
        # Create one of every combination (9 total tasks)
        $priorities = ['high', 'medium', 'low'];
        $statuses = ['pending', 'in_progress', 'done'];

        foreach ($priorities as $priority) {
            foreach ($statuses as $status) {
                Task::factory()->create([
                    'title' => "{$priority} {$status}",
                    'due_date' => '2026-04-01',
                    'priority' => $priority,
                    'status' => $status,
                ]);
            }
        }

        $response = $this->getJson('/api/v1/tasks/report?date=2026-04-01');

        $response->assertStatus(200)
            ->assertJson([
                'summary' => [
                    'high' => ['pending' => 1, 'in_progress' => 1, 'done' => 1],
                    'medium' => ['pending' => 1, 'in_progress' => 1, 'done' => 1],
                    'low' => ['pending' => 1, 'in_progress' => 1, 'done' => 1],
                ],
            ]);
    }

    /**
     * Test report counts multiple tasks correctly
     */
    public function test_report_counts_multiple_tasks_correctly(): void
    {
        # Create 5 high priority pending tasks
        for ($i = 1; $i <= 5; $i++) {
            Task::factory()->create([
                'title' => "High Pending {$i}",
                'due_date' => '2026-04-01',
                'priority' => 'high',
                'status' => 'pending',
            ]);
        }

        $response = $this->getJson('/api/v1/tasks/report?date=2026-04-01');

        $response->assertStatus(200)
            ->assertJsonPath('summary.high.pending', 5);
    }
}