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
            'typeChart' => $this->dashboardService->verificationsByType(),
            'activities' => $this->dashboardService->recentActivities(),
        ]);
    }
}
