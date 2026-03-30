<?php

namespace App\Enums;

/**
 * StatusTransition Enum
 * Defines valid status transitions for the task workflow
 * Enforces business rule: pending -> in_progress -> done (no skipping, no reverting)
 */
enum StatusTransition: string
{
    # Transition from pending to in_progress (start working on task)
    case PENDING_TO_IN_PROGRESS = 'pending_to_in_progress';

    # Transition from in_progress to done (complete task)
    case IN_PROGRESS_TO_DONE = 'in_progress_to_done';

    /**
     * Get allowed next statuses for a given current status
     * Returns empty array for terminal states (e.g., done)
     *
     * @param TaskStatus $currentStatus The current task status
     * @return array Array of allowed next TaskStatus values
     */
    public static function getAllowedNextStatuses(TaskStatus $currentStatus): array
    {
        return match ($currentStatus) {
            TaskStatus::PENDING => [TaskStatus::IN_PROGRESS->value],
            TaskStatus::IN_PROGRESS => [TaskStatus::DONE->value],
            TaskStatus::DONE => [], # Terminal state - no transitions allowed
        };
    }

    /**
     * Check if a status transition is valid
     *
     * @param TaskStatus $fromStatus Current status
     * @param TaskStatus $toStatus Requested new status
     * @return bool True if transition is allowed by business rules
     */
    public static function isValidTransition(TaskStatus $fromStatus, TaskStatus $toStatus): bool
    {
        $allowedStatuses = self::getAllowedNextStatuses($fromStatus);

        return in_array($toStatus->value, $allowedStatuses);
    }

    /**
     * Get transition description for logging or UI display
     *
     * @return string Human-readable description of the transition
     */
    public function description(): string
    {
        return match ($this) {
            self::PENDING_TO_IN_PROGRESS => 'Start task',
            self::IN_PROGRESS_TO_DONE => 'Complete task',
        };
    }
}