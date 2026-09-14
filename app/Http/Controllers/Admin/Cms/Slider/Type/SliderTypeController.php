<?php

namespace App\Http\Controllers\Admin\Cms\Slider\Type;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Slider\Type\SliderTypeRequest;
use App\Services\Cms\Slider\Type\SliderTypeService;
use Illuminate\Http\Request;

class SliderTypeController extends Controller
{
    public $type;

    public function __construct(SliderTypeService $type)
    {
        $this->type = $type;
    }

    public function index(Request $request)
    {
        return response($this->type->paginate(25, $request));
    }

    public function indexView()
    {
        return view('admin.cms.slider.type.index');
    }

    public function store(SliderTypeRequest $request)
    {
        $type = $this->type->store($request->all());
        if ($type) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function update(SliderTypeRequest $request, $id)
    {
        $type = $this->type->update($id, $request->all());
        if ($type) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function destroy($id)
    {
        if ($this->type->delete($id)) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function show($id)
    {
        if ($type = $this->type->getById($id)) {
            return response(['status' => 'OK', 'type' => $type], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function getActive()
    {
        $types = $this->type->getActive();

        return response(['types' => $types], 200);
    }
}
