<?php

namespace App\Http\Controllers\Admin\Cms\Partner;

use App\DTOs\Filters\PartnerFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Partner\PartnerRequest;
use App\Services\Cms\Partner\PartnerService;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function __construct(protected PartnerService $partnerService) {}

    public function index(Request $request)
    {
        $filter = PartnerFilterDTO::fromArray($request->all());

        return $this->partnerService->paginate($filter);
    }

    public function store(PartnerRequest $request)
    {
        if ($this->partnerService->store($request->validated())) {
            return response()->json(['status' => 'OK', 'message' => 'Partner created successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to create partner.'], 500);
    }

    public function show($id)
    {
        if ($partner = $this->partnerService->find($id)) {
            return response()->json(['status' => 'OK', 'data' => $partner], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Partner not found.'], 404);
    }

    public function update(Request $request, $id)
    {
        $partner = $this->partnerService->update($id, $request->all());
        if ($partner) {
            return response()->json(['status' => 'OK', 'message' => 'Partner updated successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to update partner.'], 500);
    }

    public function destroy($id)
    {
        if ($this->partnerService->delete($id)) {
            return response()->json(['status' => 'OK', 'message' => 'Partner deleted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to delete partner.'], 500);
    }
}
