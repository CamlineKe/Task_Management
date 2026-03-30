<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * TaskCollection
 * Transforms collection of Task models into standardized JSON response
 * Wraps data array for consistent API structure
 */
class TaskCollection extends ResourceCollection
{
    /**
     * Transform resource collection into array
     *
     * @param Request $request Current request
     * @return array Transformed collection with data wrapper
     */
    public function toArray(Request $request): array
    {
        return [
            # Wrap tasks in data key for consistent API response structure
            # Uses TaskResource to transform each individual task
            'data' => TaskResource::collection($this->collection),
        ];
    }
}