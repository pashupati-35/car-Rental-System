<?php

namespace App\Http\Controllers\Admin\Cms\Enquiry;

use App\DTOs\Filters\EnquiryFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Enquiry\EnquiryRequest;
use App\Services\Cms\Enquiry\EnquiryService;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function __construct(protected EnquiryService $enquiryService) {}

    public function index(Request $request)
    {
        $filter = EnquiryFilterDTO::fromArray($request->all());
        return $this->enquiryService->paginate($filter);
    }

    public function show(string $id)
    {
        if ($enquiry = $this->enquiryService->show($id)) {
            return response()->json(['status' => 'OK', 'data' => $enquiry], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Enquiry not found.'], 404);
    }

    public function update(EnquiryRequest $request, string $id)
    {
        $enquiry = $this->enquiryService->update($id, $request->validated());
        if ($enquiry) {
            return response()->json(['status' => 'OK', 'message' => 'Enquiry updated successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to update enquiry.'], 500);
    }

    public function destroy(string $id)
    {
        if ($this->enquiryService->delete($id)) {
            return response()->json(['status' => 'OK', 'message' => 'Enquiry deleted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to delete enquiry.'], 500);
    }

    public function store(EnquiryRequest $request)
    {
        $enquiry = $this->enquiryService->create($request->validated());
        if ($enquiry) {
            return response()->json(['status' => 'OK', 'message' => 'Enquiry created successfully.'], 201);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to create enquiry.'], 500);
    }
}
