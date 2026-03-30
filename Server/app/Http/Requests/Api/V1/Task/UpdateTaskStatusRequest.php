<?php

namespace App\Http\Requests\Api\V1\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdateTaskStatusRequest
 * Form request validation for updating task status
 * Validates status is a valid enum value
 */
class UpdateTaskStatusRequest extends FormRequest
{
    /**
     * Determine if user is authorized to make this request
     * Public API - no authentication required
     *
     * @return bool Always true
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get validation rules for status update
     *
     * @return array Validation rules array
     */
    public function rules(): array
    {
        return [
            # Status: required, must be one of pending, in_progress, done
            # Note: Transition validity (pending->in_progress->done) is checked in service layer
            'status' => [
                'required',
                'string',
                Rule::in(['pending', 'in_progress', 'done']),
            ],
        ];
    }

    /**
     * Get custom error messages for validation failures
     *
     * @return array Custom error messages
     */
    public function messages(): array
    {
        return [
            'status.required' => 'The status is required.',
            'status.in' => 'The status must be pending, in_progress, or done.',
        ];
    }
}