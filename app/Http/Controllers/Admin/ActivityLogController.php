<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\Filters\ActivityLogFilterDTO;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\ActivityLogRepositoryInterface;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct(protected ActivityLogRepositoryInterface $activityLogRepo) {}

    public function index(Request $request)
    {
        $filter = ActivityLogFilterDTO::fromArray($request->all());
        return $this->activityLogRepo->getFilteredPaginated($filter);
    }

    public function data(Request $request)
    {
        return $this->index($request);
    }

    public function getByEmployee($employeeId, Request $request)
    {
        $perPage = (int) $request->input('per_page', 20);
        return $this->activityLogRepo->getByEmployee((int) $employeeId, $perPage);
    }
}
