<?php

namespace App\Services;

use App\DTOs\ReportData;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Services\Interfaces\ReportServiceInterface;

/**
 * ReportService
 * Concrete implementation of report generation business logic
 * Handles daily task aggregation and data transformation
 */
class ReportService implements ReportServiceInterface
{
    /**
     * Create service instance with repository injection
     *
     * @param TaskRepositoryInterface $taskRepository Task data access layer
     */
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
    ) {
    }

    /**
     * Generate daily report for specific date
     * Aggregates task counts by priority and status
     * Returns structured data with all combinations (zeros for empty)
     *
     * @param string $date Date in Y-m-d format
     * @return ReportData Structured report DTO
     */
    public function generateDailyReport(string $date): ReportData
    {
        # Query database for aggregated counts
        # Uses idx_tasks_due_date_priority_status index for optimal performance
        $rows = $this->taskRepository->getDailyReport($date);

        # Transform query results into structured report format
        # Fills in zeros for missing priority/status combinations
        return ReportData::fromQueryResult($date, $rows->toArray());
    }
}