<?php

namespace App\Http\Controllers\Admin\Cms\Popup;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Popup\PopupRequest;
use App\Services\Cms\Popup\PopupService;
use Illuminate\Http\Request;

class PopupController extends Controller
{
    public function __construct(protected PopupService $popup) {}

    public function index(Request $request)
    {
        return $this->popup->paginate($request->per_pages ?? 25, $request);
    }

    public function store(PopupRequest $request)
    {
        $popup = $this->popup->store($request->validated());
        if ($popup) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function update(PopupRequest $request, $id)
    {
        $popup = $this->popup->update($id, $request->validated());
        if ($popup) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function destroy($id)
    {
        if ($this->popup->delete($id)) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function show($id)
    {
        if ($popup = $this->popup->getById($id)) {
            return response(['status' => 'OK', 'popup' => $popup], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function sort(Request $request)
    {
        if ($popup = $this->popup->sort($request->all())) {
            return response(['status' => 'OK', 'popup' => $popup], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }
}
