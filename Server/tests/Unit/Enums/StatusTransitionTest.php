<?php

namespace Tests\Unit\Enums;

use App\Enums\StatusTransition;
use App\Enums\TaskStatus;
use PHPUnit\Framework\TestCase;

/**
 * StatusTransitionTest
 * Unit tests for StatusTransition enum
 * Tests transition validation logic and helper methods
 */
class StatusTransitionTest extends TestCase
{
    /**
     * Test getAllowedNextStatuses returns correct transitions for pending
     * pending should only allow in_progress
     */
    public function test_get_allowed_next_statuses_for_pending(): void
    {
        # Get allowed transitions from pending
        $allowed = StatusTransition::getAllowedNextStatuses(TaskStatus::PENDING);

        # Assert only in_progress is allowed
        $this->assertEquals([TaskStatus::IN_PROGRESS->value], $allowed);
    }

    /**
     * Test getAllowedNextStatuses returns correct transitions for in_progress
     * in_progress should only allow done
     */
    public function test_get_allowed_next_statuses_for_in_progress(): void
    {
        # Get allowed transitions from in_progress
        $allowed = StatusTransition::getAllowedNextStatuses(TaskStatus::IN_PROGRESS);

        # Assert only done is allowed
        $this->assertEquals([TaskStatus::DONE->value], $allowed);
    }

    /**
     * Test getAllowedNextStatuses returns empty array for done
     * done is terminal state with no allowed transitions
     */
    public function test_get_allowed_next_statuses_for_done(): void
    {
        # Get allowed transitions from done
        $allowed = StatusTransition::getAllowedNextStatuses(TaskStatus::DONE);

        # Assert empty array returned
        $this->assertEquals([], $allowed);
    }

    /**
     * Test isValidTransition returns true for valid pending to in_progress
     */
    public function test_is_valid_transition_pending_to_in_progress(): void
    {
        # Validate pending -> in_progress transition
        $isValid = StatusTransition::isValidTransition(
            TaskStatus::PENDING,
            TaskStatus::IN_PROGRESS
        );

        # Assert transition is valid
        $this->assertTrue($isValid);
    }

    /**
     * Test isValidTransition returns true for valid in_progress to done
     */
    public function test_is_valid_transition_in_progress_to_done(): void
    {
        # Validate in_progress -> done transition
        $isValid = StatusTransition::isValidTransition(
            TaskStatus::IN_PROGRESS,
            TaskStatus::DONE
        );

        # Assert transition is valid
        $this->assertTrue($isValid);
    }

    /**
     * Test isValidTransition returns false for skipping pending to done
     */
    public function test_is_invalid_transition_pending_to_done(): void
    {
        # Validate pending -> done transition (skipping in_progress)
        $isValid = StatusTransition::isValidTransition(
            TaskStatus::PENDING,
            TaskStatus::DONE
        );

        # Assert transition is invalid
        $this->assertFalse($isValid);
    }

    /**
     * Test isValidTransition returns false for reverting in_progress to pending
     */
    public function test_is_invalid_transition_in_progress_to_pending(): void
    {
        # Validate in_progress -> pending transition (reverting)
        $isValid = StatusTransition::isValidTransition(
            TaskStatus::IN_PROGRESS,
            TaskStatus::PENDING
        );

        # Assert transition is invalid
        $this->assertFalse($isValid);
    }

    /**
     * Test isValidTransition returns false for any transition from done
     */
    public function test_is_invalid_transition_from_done(): void
    {
        # Validate done -> pending transition
        $toPending = StatusTransition::isValidTransition(
            TaskStatus::DONE,
            TaskStatus::PENDING
        );

        # Validate done -> in_progress transition
        $toInProgress = StatusTransition::isValidTransition(
            TaskStatus::DONE,
            TaskStatus::IN_PROGRESS
        );

        # Validate done -> done transition (self-transition)
        $toDone = StatusTransition::isValidTransition(
            TaskStatus::DONE,
            TaskStatus::DONE
        );

        # Assert all transitions from done are invalid
        $this->assertFalse($toPending);
        $this->assertFalse($toInProgress);
        $this->assertFalse($toDone);
    }

    /**
     * Test description returns correct human-readable text for PENDING_TO_IN_PROGRESS
     */
    public function test_description_for_pending_to_in_progress(): void
    {
        # Get description for PENDING_TO_IN_PROGRESS
        $description = StatusTransition::PENDING_TO_IN_PROGRESS->description();

        # Assert correct description
        $this->assertEquals('Start task', $description);
    }

    /**
     * Test description returns correct human-readable text for IN_PROGRESS_TO_DONE
     */
    public function test_description_for_in_progress_to_done(): void
    {
        # Get description for IN_PROGRESS_TO_DONE
        $description = StatusTransition::IN_PROGRESS_TO_DONE->description();

        # Assert correct description
        $this->assertEquals('Complete task', $description);
    }

    /**
     * Test enum values exist and are correct
     */
    public function test_enum_values(): void
    {
        # Assert PENDING_TO_IN_PROGRESS value
        $this->assertEquals('pending_to_in_progress', StatusTransition::PENDING_TO_IN_PROGRESS->value);

        # Assert IN_PROGRESS_TO_DONE value
        $this->assertEquals('in_progress_to_done', StatusTransition::IN_PROGRESS_TO_DONE->value);
    }
}