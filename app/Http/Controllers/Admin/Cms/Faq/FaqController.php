<?php

namespace App\Http\Controllers\Admin\Cms\Faq;

use App\DTOs\Filters\FaqFilterDTO;
use App\Http\Controllers\Controller;
use App\Services\Cms\Faq\FaqService;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function __construct(protected FaqService $faqService) {}

    public function index(Request $request)
    {
        $filter = FaqFilterDTO::fromArray($request->all());

        return $this->faqService->paginate($filter);
    }

    public function store(Request $request)
    {
        $faq = $this->faqService->store($request->all());
        if ($faq) {
            return response()->json(['status' => 'OK', 'message' => 'FAQ created successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to create FAQ.'], 500);
    }

    public function sort(Request $request)
    {
        $data = $request->all();
        $sortedIds = isset($data['ids']) ? $data['ids'] : (is_array($data) ? $data : []);
        $value = $this->faqService->sort($sortedIds);
        if ($value) {
            return response()->json(['status' => 'OK', 'message' => 'Sorted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to sort.'], 500);
    }

    public function update(Request $request, $id)
    {
        $faq = $this->faqService->update($id, $request->all());
        if ($faq) {
            return response()->json(['status' => 'OK', 'message' => 'FAQ updated successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to update FAQ.'], 500);
    }

    public function destroy($id)
    {
        if ($this->faqService->delete($id)) {
            return response()->json(['status' => 'OK', 'message' => 'FAQ deleted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to delete FAQ.'], 500);
    }

    public function show($id)
    {
        if ($faq = $this->faqService->getById($id)) {
            return response()->json(['status' => 'OK', 'faq' => $faq], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'FAQ not found.'], 404);
    }
}
