<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\Filters\EmailLogFilterDTO;
use App\Http\Controllers\Controller;
use App\Models\EmailLog\EmailLog;
use App\Repositories\Admin\EmailLogRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmailLogController extends Controller
{
    public function __construct(protected EmailLogRepositoryInterface $emailLogRepo) {}

    public function index(Request $request)
    {
        $filter = EmailLogFilterDTO::fromArray($request->all());
        $logs = $this->emailLogRepo->getFilteredPaginated($filter);

        if ($request->is('*/list') || ($request->wantsJson() && ! $request->header('X-Inertia') && ! $request->hasHeader('X-Inertia'))) {
            return response()->json($logs);
        }

        return Inertia::render('admin/email-logs/Index', [
            'logs' => $logs,
            'filters' => [
                'search' => $request->search,
                'to' => $request->to,
                'status' => $request->status,
                'sender_type' => $request->sender_type,
                'per_page' => $filter->per_page,
            ],
            'counts' => [
                'total' => EmailLog::query()->count(),
                'sent' => EmailLog::query()->where('status', 'sent')->count(),
                'failed' => EmailLog::query()->where('status', 'failed')->count(),
                'today' => EmailLog::query()->whereDate('created_at', today())->count(),
            ],
        ]);
    }

    public function data(Request $request)
    {
        $filter = EmailLogFilterDTO::fromArray($request->all());

        return response()->json($this->emailLogRepo->getFilteredPaginated($filter));
    }

    public function getByOwner($ownerId, Request $request)
    {
        $perPage = (int) $request->input('per_page', 20);
        $logs = $this->emailLogRepo->getByOwner((int) $ownerId, $perPage);

        return response()->json($logs);
    }

    public function getByCustomer($customerId, Request $request)
    {
        $perPage = (int) $request->input('per_page', 20);
        $logs = $this->emailLogRepo->getByCustomer((int) $customerId, $perPage);

        return response()->json($logs);
    }

    public function getByEmployee($employeeId, Request $request)
    {
        $perPage = (int) $request->input('per_page', 20);

        return response()->json($this->emailLogRepo->getByEmployee((int) $employeeId, $perPage));
    }

    public function show($id)
    {
        $log = $this->emailLogRepo->find($id);

        return response()->json(['status' => 'OK', 'data' => $log]);
    }

    public function preview($id)
    {
        $log = $this->emailLogRepo->findOrFail($id);

        return response()->json([
            'status' => 'OK',
            'preview' => $log->body ?? $log->content ?? '',
            'data' => $log,
        ]);
    }

    public function destroy($id, Request $request)
    {
        $this->emailLogRepo->delete($id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => 'OK', 'message' => 'Email log deleted.']);
        }

        return redirect()->back()->with('status', 'Email log deleted successfully.');
    }
}
