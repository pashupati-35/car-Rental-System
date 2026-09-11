<?php

namespace App\Http\Controllers\Admin\Cms\Notice;

use App\DTOs\Filters\NoticeFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Notice\NoticeRequest;
use App\Services\Cms\Notice\NoticeService;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function __construct(protected NoticeService $noticeService) {}

    public function index(Request $request)
    {
        $filter = NoticeFilterDTO::fromArray($request->all());

        return $this->noticeService->paginate($filter);
    }

    public function sort(Request $request)
    {
        $data = $request->all();
        $sortedIds = isset($data['ids']) ? $data['ids'] : (is_array($data) ? $data : []);
        if ($this->noticeService->sort($sortedIds)) {
            return response()->json(['status' => 'OK', 'message' => 'Sorted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to sort.'], 500);
    }

    public function store(NoticeRequest $request)
    {
        if ($this->noticeService->store($request->validated())) {
            return response()->json(['status' => 'OK', 'message' => 'Notice created successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to create notice.'], 500);
    }

    public function show($id)
    {
        if ($notice = $this->noticeService->find($id)) {
            return response()->json(['status' => 'OK', 'data' => $notice], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Notice not found.'], 404);
    }

    public function update(NoticeRequest $request, $id)
    {
        $notice = $this->noticeService->update($id, $request->validated());
        if ($notice) {
            return response()->json(['status' => 'OK', 'message' => 'Notice updated successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to update notice.'], 500);
    }

    public function destroy($id)
    {
        if ($this->noticeService->delete($id)) {
            return response()->json(['status' => 'OK', 'message' => 'Notice deleted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to delete notice.'], 500);
    }
}
