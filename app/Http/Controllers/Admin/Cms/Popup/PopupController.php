<?php

namespace App\Http\Controllers\Admin\Cms\Popup;

use App\DTOs\Filters\PopupFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Popup\PopupRequest;
use App\Services\Cms\Popup\PopupService;
use Illuminate\Http\Request;

class PopupController extends Controller
{
    public function __construct(protected PopupService $popupService) {}

    public function index(Request $request)
    {
        $filter = PopupFilterDTO::fromArray($request->all());
        return $this->popupService->paginate($filter);
    }

    public function store(PopupRequest $request)
    {
        $popup = $this->popupService->store($request->validated());
        if ($popup) {
            return response()->json(['status' => 'OK', 'message' => 'Popup created successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to create popup.'], 500);
    }

    public function update(Request $request, $id)
    {
        $popup = $this->popupService->update($id, $request->all());
        if ($popup) {
            return response()->json(['status' => 'OK', 'message' => 'Popup updated successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to update popup.'], 500);
    }

    public function destroy($id)
    {
        if ($this->popupService->delete($id)) {
            return response()->json(['status' => 'OK', 'message' => 'Popup deleted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to delete popup.'], 500);
    }

    public function show($id)
    {
        if ($popup = $this->popupService->getById($id)) {
            return response()->json(['status' => 'OK', 'data' => $popup], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Popup not found.'], 404);
    }

    public function sort(Request $request)
    {
        $data = $request->all();
        $sortedIds = isset($data['ids']) ? $data['ids'] : (is_array($data) ? $data : []);
        if ($this->popupService->sort($sortedIds)) {
            return response()->json(['status' => 'OK', 'message' => 'Sorted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to sort.'], 500);
    }
}
