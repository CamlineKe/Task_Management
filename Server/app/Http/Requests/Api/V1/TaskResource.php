<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * TaskResource
 * Transforms Task model into standardized JSON response
 * Used for single task responses
 */
class TaskResource extends JsonResource
{
    /**
     * Transform resource into array
     *
     * @param Request $request Current request
     * @return array Transformed task data
     */
    public function toArray(Request $request): array
    {
        return [
            # Task identification
            'id' => $this->id,

            # Task content
            'title' => $this->title,

            # Scheduling
            'due_date' => $this->due_date->format('Y-m-d'),

            # Categorization
            'priority' => $this->priority,

            # Workflow state
            'status' => $this->status,

            # Metadata
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}