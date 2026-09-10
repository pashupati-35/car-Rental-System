<?php

namespace App\Http\Controllers\Admin\Cms\Faq;

use App\Http\Controllers\Controller;
use App\Services\Cms\Faq\FaqService;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    protected $faq;

    public function __construct(FaqService $faq)
    {
        $this->faq = $faq;
    }

    public function index(Request $request)
    {
        return $this->faq->paginate(25, $request);
    }

    public function store(Request $request)
    {
        $faq = $this->faq->store($request->all());
        if ($faq) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function sort(Request $request)
    {
        $value = $this->faq->sort($request->all());
        if ($value) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function update(Request $request, $id)
    {
        $faq = $this->faq->update($id, $request->all());
        if ($faq) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function destroy($id)
    {
        if ($this->faq->delete($id)) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function show($id)
    {
        if ($faq = $this->faq->getById($id)) {
            return response(['status' => 'OK', 'faq' => $faq], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }
}
