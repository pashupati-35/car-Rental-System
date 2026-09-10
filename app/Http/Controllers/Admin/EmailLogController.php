<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\Filters\EmailLogFilterDTO;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\EmailLogRepositoryInterface;
use Illuminate\Http\Request;

class EmailLogController extends Controller
{
    public function __construct(protected EmailLogRepositoryInterface $emailLogRepo) {}

    public function index(Request $request)
    {
        $filter = EmailLogFilterDTO::fromArray($request->all());
        return $this->emailLogRepo->getFilteredPaginated($filter);
    }

    public function data(Request $request)
    {
        return $this->index($request);
    }

    public function getByEmployee($employeeId, Request $request)
    {
        $perPage = (int) $request->input('per_page', 20);
        return $this->emailLogRepo->getByEmployee((int) $employeeId, $perPage);
    }

    public function show($id)
    {
        $log = $this->emailLogRepo->find($id);
        return response()->json(['status' => 'OK', 'data' => $log]);
    }

    public function preview($id)
    {
        $log = $this->emailLogRepo->findOrFail($id);
        return response()->json(['status' => 'OK', 'preview' => $log->content ?? $log->body ?? '']);
    }

    public function destroy($id)
    {
        $this->emailLogRepo->delete($id);
        return response()->json(['status' => 'OK', 'message' => 'Email log deleted.']);
    }
}
