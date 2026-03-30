<?php

namespace App\Services\Interfaces;

use App\DTOs\ReportData;

/**
 * ReportServiceInterface
 * Contract for report generation business logic
 * Defines daily report operations for dependency injection and testing
 */
interface ReportServiceInterface
{
    /**
     * Generate daily task report for specific date
     * Aggregates tasks by priority and status
     *
     * @param string $date Date in Y-m-d format
     * @return ReportData Structured report with counts per priority and status
     */
    public function generateDailyReport(string $date): ReportData;
}