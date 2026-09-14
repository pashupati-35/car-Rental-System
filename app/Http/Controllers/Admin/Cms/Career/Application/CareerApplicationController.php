<?php

namespace App\Http\Controllers\Admin\Cms\Career\Application;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Career\Application\CareerApplicationRequest;
use App\Services\Cms\Career\Application\CareerApplicationService;
use Illuminate\Http\Request;

class CareerApplicationController extends Controller
{
    public function __construct(protected CareerApplicationService $application) {}

    public function index($careerId, Request $request)
    {
        return $this->application->paginate($request->per_pages ?? 25, $request, $careerId);
    }

    public function all()
    {
        $application = $this->application->all();

        return response(['applications' => $application]);
    }

    public function store(CareerApplicationRequest $request, $careerId)
    {
        $application = $this->application->store($request->validated(), $careerId);
        if ($application) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function show($careerId, $id)
    {
        if ($application = $this->application->getById($id)) {
            return response(['status' => 'OK', 'application' => $application], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function update(CareerApplicationRequest $request, $careerId, $id)
    {
        $application = $this->application->update($id, $request->validated(), $careerId);
        if ($application) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function destroy($careerId, $id)
    {
        if ($this->application->delete($id)) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }
}
