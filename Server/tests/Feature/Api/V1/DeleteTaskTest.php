<?php

namespace Tests\Feature\Api\V1;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * DeleteTaskTest
 * Feature tests for DELETE /api/v1/tasks/{id} endpoint
 * Tests deletion rules, permissions, and error handling
 */
class DeleteTaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful deletion of done task
     * Returns 204 No Content
     */
    public function test_can_delete_done_task(): void
    {
        # Create task with done status
        $task = Task::factory()->create([
            'status' => 'done',
            'title' => 'Completed Task',
        ]);

        # Send DELETE request
        $response = $this->deleteJson("/api/v1/tasks/{$task->id}");

        # Assert 204 No Content status
        $response->assertStatus(204);

        # Assert empty response body
        $response->assertNoContent();

        # Assert task removed from database
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    /**
     * Test cannot delete pending task
     * Returns 403 Forbidden
     */
    public function test_cannot_delete_pending_task(): void
    {
        # Create task with pending status
        $task = Task::factory()->create([
            'status' => 'pending',
            'title' => 'Pending Task',
        ]);

        # Send DELETE request
        $response = $this->deleteJson("/api/v1/tasks/{$task->id}");

        # Assert 403 Forbidden
        $response->assertStatus(403);

        # Assert error message
        $response->assertJson([
            'message' => 'Only completed tasks can be deleted',
        ]);

        # Assert task still exists in database
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'pending',
        ]);
    }

    /**
     * Test cannot delete in_progress task
     * Returns 403 Forbidden
     */
    public function test_cannot_delete_in_progress_task(): void
    {
        # Create task with in_progress status
        $task = Task::factory()->create([
            'status' => 'in_progress',
            'title' => 'In Progress Task',
        ]);

        # Send DELETE request
        $response = $this->deleteJson("/api/v1/tasks/{$task->id}");

        # Assert 403 Forbidden
        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Only completed tasks can be deleted',
            ]);

        # Assert task still exists
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'in_progress',
        ]);
    }

    /**
     * Test delete non-existent task returns 404
     */
    public function test_delete_nonexistent_task_returns_404(): void
    {
        # Send DELETE request for non-existent ID
        $response = $this->deleteJson('/api/v1/tasks/99999');

        # Assert 404 Not Found
        $response->assertStatus(404);
    }

    /**
     * Test multiple done tasks can be deleted
     */
    public function test_can_delete_multiple_done_tasks(): void
    {
        # Create multiple done tasks
        $task1 = Task::factory()->create(['status' => 'done', 'title' => 'Done 1']);
        $task2 = Task::factory()->create(['status' => 'done', 'title' => 'Done 2']);
        $task3 = Task::factory()->create(['status' => 'done', 'title' => 'Done 3']);

        # Delete all three
        $this->deleteJson("/api/v1/tasks/{$task1->id}")->assertStatus(204);
        $this->deleteJson("/api/v1/tasks/{$task2->id}")->assertStatus(204);
        $this->deleteJson("/api/v1/tasks/{$task3->id}")->assertStatus(204);

        # Assert all removed
        $this->assertDatabaseCount('tasks', 0);
    }

    /**
     * Test done task deletion is permanent (hard delete)
     */
    public function test_done_task_deletion_is_permanent(): void
    {
        # Create and delete done task
        $task = Task::factory()->create([
            'status' => 'done',
            'title' => 'To Be Deleted',
        ]);

        $taskId = $task->id;

        # Delete the task
        $this->deleteJson("/api/v1/tasks/{$taskId}")->assertStatus(204);

        # Verify completely removed (not soft deleted)
        $this->assertDatabaseMissing('tasks', ['id' => $taskId]);
    }
}