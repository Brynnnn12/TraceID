<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService) {}

    public function index()
    {
        return view('dashboard', [
            'statistics' => $this->dashboardService->statistics(),
            'verificationsChart' => $this->dashboardService->verificationsLast7Days(),
            'statusChart' => $this->dashboardService->casesByStatus(),
            'activities' => $this->dashboardService->recentActivities(),
        ]);
    }
}
