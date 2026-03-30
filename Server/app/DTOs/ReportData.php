<?php

namespace App\DTOs;

/**
 * ReportData DTO
 * Data Transfer Object for daily task report aggregation
 * Structures the report summary by priority and status
 */
class ReportData
{
    /**
     * Create new ReportData instance
     *
     * @param string $date The report date in Y-m-d format
     * @param array $summary Nested array with priority -> status -> count structure
     */
    public function __construct(
        public readonly string $date,
        public readonly array $summary,
    ) {
    }

    /**
     * Create empty report structure for a given date
     * Initializes all priority/status combinations with zero counts
     *
     * @param string $date The report date
     * @return self ReportData with zeroed summary structure
     */
    public static function empty(string $date): self
    {
        return new self(
            date: $date,
            summary: [
                # High priority counts by status
                'high' => [
                    'pending' => 0,
                    'in_progress' => 0,
                    'done' => 0,
                ],
                # Medium priority counts by status
                'medium' => [
                    'pending' => 0,
                    'in_progress' => 0,
                    'done' => 0,
                ],
                # Low priority counts by status
                'low' => [
                    'pending' => 0,
                    'in_progress' => 0,
                    'done' => 0,
                ],
            ],
        );
    }

    /**
     * Create report from database query result
     * Transforms flat query results into nested summary structure
     *
     * @param string $date The report date
     * @param array $rows Array of objects with priority, status, total properties
     * @return self Populated ReportData instance
     */
    public static function fromQueryResult(string $date, array $rows): self
    {
        # Start with empty structure
        $report = self::empty($date);
        $summary = $report->summary;

        # Fill in actual counts from query results
        foreach ($rows as $row) {
            # Handle both array and object row formats
            $priority = is_array($row) ? $row['priority'] : $row->priority;
            $status = is_array($row) ? $row['status'] : $row->status;
            $total = is_array($row) ? $row['total'] : $row->total;

            # Only update if valid priority/status combination
            if (isset($summary[$priority][$status])) {
                $summary[$priority][$status] = (int) $total;
            }
        }

        return new self(
            date: $date,
            summary: $summary,
        );
    }

    /**
     * Convert report to array for JSON response
     *
     * @return array Array with date and summary keys
     */
    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'summary' => $this->summary,
        ];
    }
}