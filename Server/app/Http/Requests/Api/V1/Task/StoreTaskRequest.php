<?php

namespace App\Http\Requests\Api\V1\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * StoreTaskRequest
 * Form request validation for creating new tasks
 * Validates title, due_date, and priority with strict rules
 */
class StoreTaskRequest extends FormRequest
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
     * Get validation rules for task creation
     *
     * @return array Validation rules array
     */
    public function rules(): array
    {
        return [
            # Title: required string, checked for uniqueness with due_date in controller/service
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            # Due date: required, valid date format, must be today or future
            # Prevents creating tasks with past due dates
            'due_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'after_or_equal:today',
            ],

            # Priority: required, must be one of low, medium, high
            'priority' => [
                'required',
                'string',
                Rule::in(['low', 'medium', 'high']),
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
            'title.required' => 'The task title is required.',
            'title.max' => 'The title may not exceed 255 characters.',

            'due_date.required' => 'The due date is required.',
            'due_date.date_format' => 'The due date must be in YYYY-MM-DD format.',
            'due_date.after_or_equal' => 'The due date must be today or a future date.',

            'priority.required' => 'The priority is required.',
            'priority.in' => 'The priority must be low, medium, or high.',
        ];
    }
}