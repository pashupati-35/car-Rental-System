<?php

namespace App\Http\Controllers\Admin\Cms\Notice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Notice\NoticeRequest;
use App\Http\Resources\Cms\Notice\NoticeResource;
use App\Services\Cms\Notice\NoticeService;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function __construct(protected NoticeService $notice) {}

    public function index(Request $request)
    {
        return $this->notice->paginate($request->per_pages ?? 25, $request);
    }

    public function all()
    {
        $notices = $this->notice->all();

        return response(['notices' => $notices]);
    }

    public function sort(Request $request)
    {
        if ($this->notice->sort($request->all())) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 200);
    }

    public function store(NoticeRequest $request)
    {
        if ($this->notice->store($request->validated())) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 200);
    }

    public function show($id)
    {
        if ($notice = $this->notice->find($id)) {
            return response(['status' => 'OK', 'notice' => new NoticeResource($notice)], 200);
        }

        return response(['status' => 'ERROR'], 200);
    }

    public function update(NoticeRequest $request, $id)
    {
        $notice = $this->notice->update($id, $request->validated());
        if ($notice) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 200);
    }

    public function destroy($id)
    {
        if ($this->notice->delete($id)) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 200);
    }
}
