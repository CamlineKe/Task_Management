<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * TaskCollection
 * Transforms a collection of Task models to JSON API response format
 */
class TaskCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'due_date' => $task->due_date->format('Y-m-d'),
                    'priority' => $task->priority,
                    'status' => $task->status,
                    'created_at' => $task->created_at->toIso8601String(),
                    'updated_at' => $task->updated_at->toIso8601String(),
                ];
            }),
        ];
    }
}
