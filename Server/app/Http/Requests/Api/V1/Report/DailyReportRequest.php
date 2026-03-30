<?php

namespace App\Http\Requests\Api\V1\Report;

use Illuminate\Foundation\Http\FormRequest;

/**
 * DailyReportRequest
 * Form request validation for daily report endpoint
 * Validates date parameter format and validity
 */
class DailyReportRequest extends FormRequest
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
     * Get validation rules for daily report
     *
     * @return array Validation rules array
     */
    public function rules(): array
    {
        return [
            # Date: required, valid date, must match Y-m-d format
            # 'date' rule ensures valid calendar date (rejects Feb 30, etc.)
            # 'date_format' ensures exact format for database query
            'date' => [
                'required',
                'date',
                'date_format:Y-m-d',
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
            'date.required' => 'The date field is required.',
            'date.date' => 'The date is not a valid date.',
            'date.date_format' => 'The date does not match the format YYYY-MM-DD.',
        ];
    }
}