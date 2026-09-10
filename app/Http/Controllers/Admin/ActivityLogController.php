<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\Filters\ActivityLogFilterDTO;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog\ActivityLog;
use App\Repositories\Admin\ActivityLogRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function __construct(protected ActivityLogRepositoryInterface $activityLogRepo) {}

    public function index(Request $request)
    {
        $filter = ActivityLogFilterDTO::fromArray($request->all());
        $logs = $this->activityLogRepo->getFilteredPaginated($filter);

        if ($request->wantsJson() || $request->is('*/list') || $request->ajax()) {
            return response()->json($logs);
        }

        $logTypes = ActivityLog::query()->select('log_type')->distinct()->pluck('log_type')->filter()->values();

        return Inertia::render('admin/activity-logs/Index', [
            'logs' => $logs,
            'filters' => [
                'search' => $request->search,
                'log_type' => $request->log_type,
                'causer_type' => $request->causer_type,
                'per_page' => $filter->per_page,
            ],
            'logTypes' => $logTypes,
            'counts' => [
                'total' => ActivityLog::query()->count(),
                'today' => ActivityLog::query()->whereDate('created_at', today())->count(),
            ],
        ]);
    }

    public function data(Request $request)
    {
        $filter = ActivityLogFilterDTO::fromArray($request->all());
        return response()->json($this->activityLogRepo->getFilteredPaginated($filter));
    }

    public function getByOwner($ownerId, Request $request)
    {
        $perPage = (int) $request->input('per_page', 20);
        $logs = $this->activityLogRepo->getByOwner((int) $ownerId, $perPage);
        return response()->json($logs);
    }

    public function getByCustomer($customerId, Request $request)
    {
        $perPage = (int) $request->input('per_page', 20);
        $logs = $this->activityLogRepo->getByCustomer((int) $customerId, $perPage);
        return response()->json($logs);
    }

    public function getByEmployee($employeeId, Request $request)
    {
        $perPage = (int) $request->input('per_page', 20);
        return response()->json($this->activityLogRepo->getByEmployee((int) $employeeId, $perPage));
    }
}
