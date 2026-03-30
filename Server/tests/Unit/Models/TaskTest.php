<?php

namespace Tests\Unit\Models;

use App\Models\Task;
use Tests\TestCase;

/**
 * TaskTest
 * Unit tests for Task model business logic
 * Tests status transition rules and model methods
 */
class TaskTest extends TestCase
{
    /**
     * Test canTransitionTo allows valid transitions
     * pending -> in_progress should be allowed
     */
    public function test_can_transition_from_pending_to_in_progress(): void
    {
        # Create task with pending status
        $task = new Task(['status' => 'pending']);

        # Assert transition to in_progress is allowed
        $this->assertTrue($task->canTransitionTo('in_progress'));
    }

    /**
     * Test canTransitionTo allows valid transitions
     * in_progress -> done should be allowed
     */
    public function test_can_transition_from_in_progress_to_done(): void
    {
        # Create task with in_progress status
        $task = new Task(['status' => 'in_progress']);

        # Assert transition to done is allowed
        $this->assertTrue($task->canTransitionTo('done'));
    }

    /**
     * Test canTransitionTo blocks invalid transitions
     * pending -> done should NOT be allowed (skipping in_progress)
     */
    public function test_cannot_skip_from_pending_to_done(): void
    {
        # Create task with pending status
        $task = new Task(['status' => 'pending']);

        # Assert direct transition to done is blocked
        $this->assertFalse($task->canTransitionTo('done'));
    }

    /**
     * Test canTransitionTo blocks reverting transitions
     * in_progress -> pending should NOT be allowed (reverting)
     */
    public function test_cannot_revert_from_in_progress_to_pending(): void
    {
        # Create task with in_progress status
        $task = new Task(['status' => 'in_progress']);

        # Assert reverting to pending is blocked
        $this->assertFalse($task->canTransitionTo('pending'));
    }

    /**
     * Test canTransitionTo blocks all transitions from done
     * done is terminal state - no transitions allowed
     */
    public function test_done_is_terminal_state_no_transitions_allowed(): void
    {
        # Create task with done status
        $task = new Task(['status' => 'done']);

        # Assert no transitions are allowed from done
        $this->assertFalse($task->canTransitionTo('pending'));
        $this->assertFalse($task->canTransitionTo('in_progress'));
        $this->assertFalse($task->canTransitionTo('done'));
    }

    /**
     * Test getAllowedTransitions returns correct next statuses
     * pending should only allow in_progress
     */
    public function test_get_allowed_transitions_for_pending(): void
    {
        # Create task with pending status
        $task = new Task(['status' => 'pending']);

        # Get allowed transitions
        $allowed = $task->getAllowedTransitions();

        # Assert only in_progress is allowed
        $this->assertEquals(['in_progress'], $allowed);
    }

    /**
     * Test getAllowedTransitions returns correct next statuses
     * in_progress should only allow done
     */
    public function test_get_allowed_transitions_for_in_progress(): void
    {
        # Create task with in_progress status
        $task = new Task(['status' => 'in_progress']);

        # Get allowed transitions
        $allowed = $task->getAllowedTransitions();

        # Assert only done is allowed
        $this->assertEquals(['done'], $allowed);
    }

    /**
     * Test getAllowedTransitions returns empty array for done
     */
    public function test_get_allowed_transitions_for_done(): void
    {
        # Create task with done status
        $task = new Task(['status' => 'done']);

        # Get allowed transitions
        $allowed = $task->getAllowedTransitions();

        # Assert empty array returned
        $this->assertEquals([], $allowed);
    }

    /**
     * Test default status is pending on new instance
     */
    public function test_default_status_is_pending(): void
    {
        # Create new task without setting status
        $task = new Task();

        # Assert status defaults to pending
        $this->assertEquals('pending', $task->status);
    }

    /**
     * Test fillable attributes are mass assignable
     */
    public function test_fillable_attributes(): void
    {
        # Create task with mass assigned data
        $data = [
            'title' => 'Test Task',
            'due_date' => '2026-03-29',
            'priority' => 'high',
            'status' => 'in_progress',
        ];

        $task = new Task($data);

        # Assert all data is set
        $this->assertEquals('Test Task', $task->title);
        $this->assertEquals('2026-03-29', $task->due_date->format('Y-m-d'));
        $this->assertEquals('high', $task->priority);
        $this->assertEquals('in_progress', $task->status);
    }

    /**
     * Test canTransitionTo handles invalid status gracefully
     */
    public function test_can_transition_handles_invalid_new_status(): void
    {
        # Create task with pending status
        $task = new Task(['status' => 'pending']);

        # Assert invalid status is not allowed
        $this->assertFalse($task->canTransitionTo('invalid_status'));
    }
}