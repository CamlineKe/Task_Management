<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Report\DailyReportRequest;
use App\Services\Interfaces\ReportServiceInterface;

/**
 * ReportController
 * Handles report generation API endpoints
 * Delegates business logic to ReportService
 */
class ReportController extends Controller
{
    /**
     * Create controller with service injection
     *
     * @param ReportServiceInterface $reportService Report business logic layer
     */
    public function __construct(
        private readonly ReportServiceInterface $reportService,
    ) {
    }

    /**
     * GET /api/v1/tasks/report
     * Generate daily task report for specific date
     * Aggregates counts by priority and status
     *
     * @param DailyReportRequest $request Validated date parameter
     * @return \Illuminate\Http\JsonResponse Structured report JSON
     */
    public function daily(DailyReportRequest $request): \Illuminate\Http\JsonResponse
    {
        # Get validated date
        $date = $request->validated()['date'];

        # Generate report via service layer
        $reportData = $this->reportService->generateDailyReport($date);

        # Return structured report
        return response()->json($reportData->toArray());
    }
}