<?php

namespace App\Http\Controllers\Admin\Cms\NewsAndUpdates;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\NewsAndUpdate\NewsAndUpdateRequest;
use App\Services\Cms\NewsAndUpdates\NewsAndUpdatesService;
use Illuminate\Http\Request;

class NewsAndUpdatesController extends Controller
{
    public function __construct(protected NewsAndUpdatesService $newsandupdates) {}

    public function index(Request $request)
    {
        return $this->newsandupdates->paginate($request->per_pages ?? 20, $request);
    }

    public function store(NewsAndUpdateRequest $request)
    {
        if ($this->newsandupdates->store($request->validated())) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 200);
    }

    public function show($id)
    {
        if ($newsandupdates = $this->newsandupdates->find($id)) {
            return response(['status' => 'OK', 'newsandupdates' => $newsandupdates], 200);
        }

        return response(['status' => 'ERROR'], 200);
    }

    public function edit($id)
    {
        return view('admin.cms.new-and-update.edit', compact('id'));
    }

    public function update(NewsAndUpdateRequest $request, $id)
    {
        $newsandupdates = $this->newsandupdates->update($id, $request->validated());
        if ($newsandupdates) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 200);
    }

    public function destroy($id)
    {
        if ($this->newsandupdates->delete($id)) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 200);
    }
}
