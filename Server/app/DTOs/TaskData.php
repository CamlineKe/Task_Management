<?php

namespace App\DTOs;

/**
 * TaskData DTO
 * Data Transfer Object for task creation and updates
 * Ensures type-safe data passing between layers
 */
class TaskData
{
    /**
     * Create new TaskData instance
     *
     * @param string $title Task title
     * @param string $dueDate Due date in Y-m-d format
     * @param string $priority Priority level (low, medium, high)
     * @param string|null $status Optional status override (defaults to pending)
     */
    public function __construct(
        public readonly string $title,
        public readonly string $dueDate,
        public readonly string $priority,
        public readonly ?string $status = null,
    ) {
    }

    /**
     * Create DTO from array (e.g., validated request data)
     *
     * @param array $data Array containing title, due_date, priority, optional status
     * @return self New TaskData instance
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            dueDate: $data['due_date'],
            priority: $data['priority'],
            status: $data['status'] ?? null,
        );
    }

    /**
     * Convert DTO to array for model creation/update
     *
     * @return array Array with database column names as keys
     */
    public function toArray(): array
    {
        $array = [
            'title' => $this->title,
            'due_date' => $this->dueDate,
            'priority' => $this->priority,
        ];

        # Only include status if explicitly provided (otherwise model uses default)
        if ($this->status !== null) {
            $array['status'] = $this->status;
        }

        return $array;
    }
}