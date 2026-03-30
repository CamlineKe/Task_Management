<?php

namespace Tests\Feature\Api\V1;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * UpdateTaskStatusTest
 * Feature tests for PATCH /api/v1/tasks/{id}/status endpoint
 * Tests status transitions, validation, and error handling
 */
class UpdateTaskStatusTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test valid transition from pending to in_progress
     * Returns 200 with updated task
     */
    public function test_can_transition_from_pending_to_in_progress(): void
    {
        # Create task with pending status
        $task = Task::factory()->create([
            'status' => 'pending',
            'title' => 'Start This Task',
        ]);

        # Send PATCH request
        $response = $this->patchJson("/api/v1/tasks/{$task->id}/status", [
            'status' => 'in_progress',
        ]);

        # Assert response status 200
        $response->assertStatus(200);

        # Assert response structure (wrapped in data key)
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

        # Assert status updated (wrapped in data key)
        $response->assertJsonFragment([
            'id' => $task->id,
            'status' => 'in_progress',
        ]);

        # Assert database updated
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'in_progress',
        ]);
    }

    /**
     * Test valid transition from in_progress to done
     */
    public function test_can_transition_from_in_progress_to_done(): void
    {
        # Create task with in_progress status
        $task = Task::factory()->create([
            'status' => 'in_progress',
            'title' => 'Complete This Task',
        ]);

        $response = $this->patchJson("/api/v1/tasks/{$task->id}/status", [
            'status' => 'done',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $task->id,
                'status' => 'done',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'done',
        ]);
    }

    /**
     * Test invalid transition from pending to done returns 422
     * Cannot skip in_progress status
     */
    public function test_cannot_skip_from_pending_to_done(): void
    {
        # Create task with pending status
        $task = Task::factory()->create([
            'status' => 'pending',
            'title' => 'Skip Attempt',
        ]);

        $response = $this->patchJson("/api/v1/tasks/{$task->id}/status", [
            'status' => 'done',
        ]);

        # Assert 422 Unprocessable Entity
        $response->assertStatus(422);

        # Assert error message
        $response->assertJson([
            'message' => 'Invalid status transition',
            'current_status' => 'pending',
            'requested_status' => 'done',
        ]);

        # Assert database not updated
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'pending',
        ]);
    }

    /**
     * Test invalid transition from in_progress to pending returns 422
     * Cannot revert to previous status
     */
    public function test_cannot_revert_from_in_progress_to_pending(): void
    {
        # Create task with in_progress status
        $task = Task::factory()->create([
            'status' => 'in_progress',
            'title' => 'Revert Attempt',
        ]);

        $response = $this->patchJson("/api/v1/tasks/{$task->id}/status", [
            'status' => 'pending',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Invalid status transition',
                'current_status' => 'in_progress',
                'requested_status' => 'pending',
            ]);

        # Assert status unchanged
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'in_progress',
        ]);
    }

    /**
     * Test no transitions allowed from done status
     */
    public function test_no_transitions_allowed_from_done(): void
    {
        # Create task with done status
        $task = Task::factory()->create([
            'status' => 'done',
            'title' => 'Terminal Task',
        ]);

        # Try to transition to pending
        $response1 = $this->patchJson("/api/v1/tasks/{$task->id}/status", [
            'status' => 'pending',
        ]);
        $response1->assertStatus(422);

        # Try to transition to in_progress
        $response2 = $this->patchJson("/api/v1/tasks/{$task->id}/status", [
            'status' => 'in_progress',
        ]);
        $response2->assertStatus(422);

        # Assert status remains done
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'done',
        ]);
    }

    /**
     * Test update non-existent task returns 404
     */
    public function test_update_nonexistent_task_returns_404(): void
    {
        $response = $this->patchJson('/api/v1/tasks/99999/status', [
            'status' => 'in_progress',
        ]);

        $response->assertStatus(404);
    }

    /**
     * Test validation fails when status is missing
     */
    public function test_cannot_update_without_status(): void
    {
        $task = Task::factory()->create(['status' => 'pending']);

        $response = $this->patchJson("/api/v1/tasks/{$task->id}/status", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    /**
     * Test validation fails for invalid status value
     */
    public function test_cannot_update_with_invalid_status(): void
    {
        $task = Task::factory()->create(['status' => 'pending']);

        $response = $this->patchJson("/api/v1/tasks/{$task->id}/status", [
            'status' => 'invalid_status',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    /**
     * Test self-transition (pending to pending) is not allowed by business rules
     * Though technically same status, should be handled gracefully
     */
    public function test_self_transition_pending_to_pending(): void
    {
        $task = Task::factory()->create(['status' => 'pending']);

        $response = $this->patchJson("/api/v1/tasks/{$task->id}/status", [
            'status' => 'pending',
        ]);

        # Should fail because pending is not in allowed transitions for pending
        $response->assertStatus(422);
    }
}