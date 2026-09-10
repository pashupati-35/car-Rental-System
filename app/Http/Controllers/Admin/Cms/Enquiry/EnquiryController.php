<?php

namespace App\Http\Controllers\Admin\Cms\Enquiry;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Enquiry\EnquiryRequest;
use App\Services\Cms\Enquiry\EnquiryService;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function __construct(protected EnquiryService $enquiryService) {}

    public function index(Request $request)
    {
        $enquiry = $this->enquiryService->paginate($request->per_pages ?? 10, $request);
        if ($enquiry) {
            return response(['data' => $enquiry], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function show(string $id)
    {
        $enquiry = $this->enquiryService->show($id);
        if ($enquiry) {
            return response(['data' => $enquiry], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function update(EnquiryRequest $request, string $id)
    {
        $enquiry = $this->enquiryService->update($id, $request->all());
        if ($enquiry) {
            return response(['data' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function destroy(string $id)
    {
        $enquiry = $this->enquiryService->delete($id);
        if ($enquiry) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function store(EnquiryRequest $request)
    {
        $enquiry = $this->enquiryService->create($request->all());
        if ($enquiry) {
            return response(['data' => 'OK'], 201);
        }

        return response(['status' => 'ERROR'], 500);
    }
}
