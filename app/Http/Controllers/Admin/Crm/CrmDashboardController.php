<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use App\Services\Crm\CrmDashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CrmDashboardController extends Controller
{
    public function __construct(
        protected CrmDashboardService $dashboardService
    ) {}

    public function index(Request $request): Response
    {
        $dashboardData = $this->dashboardService->getDashboardMetrics();

        return Inertia::render('admin/crm/Dashboard', [
            'data' => $dashboardData,
        ]);
    }
}
