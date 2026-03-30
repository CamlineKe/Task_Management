<?php

namespace App\Enums;

/**
 * TaskStatus Enum
 * Type-safe enum for task status values
 * Used for status validation and type safety
 */
enum TaskStatus: string
{
    # Pending - task created but not started
    case PENDING = 'pending';

    # In Progress - task currently being worked on
    case IN_PROGRESS = 'in_progress';

    # Done - task completed
    case DONE = 'done';

    /**
     * Get all enum values as array
     * Useful for validation rules and dropdown options
     *
     * @return array Array of string values
     */
    public static function values(): array
    {
        return [
            self::PENDING->value,
            self::IN_PROGRESS->value,
            self::DONE->value,
        ];
    }
}