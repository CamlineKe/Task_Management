<?php

namespace App\Enums;

/**
 * TaskPriority Enum
 * Type-safe enum for task priority levels
 * Used for priority validation and type safety
 */
enum TaskPriority: string
{
    # Low priority - least urgent tasks
    case LOW = 'low';

    # Medium priority - standard urgency tasks
    case MEDIUM = 'medium';

    # High priority - most urgent tasks
    case HIGH = 'high';

    /**
     * Get all enum values as array
     * Useful for validation rules and dropdown options
     *
     * @return array Array of string values
     */
    public static function values(): array
    {
        return [
            self::LOW->value,
            self::MEDIUM->value,
            self::HIGH->value,
        ];
    }
}