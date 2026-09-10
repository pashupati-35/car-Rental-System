<?php

namespace App\Http\Controllers\Admin\Cms\ContactUs;

use App\DTOs\Filters\ContactUsFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactUs\ContactUsRequest;
use App\Services\Cms\ContactUs\ContactUsService;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    public function __construct(protected ContactUsService $contactService) {}

    public function index(Request $request)
    {
        $filter = ContactUsFilterDTO::fromArray($request->all());
        return $this->contactService->paginate($filter);
    }

    public function store(ContactUsRequest $request)
    {
        $contact = $this->contactService->store($request->validated());
        if ($contact) {
            return response()->json(['status' => 'OK', 'message' => 'Contact created successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to create contact.'], 500);
    }

    public function update(Request $request, $id)
    {
        $contact = $this->contactService->update($id, $request->all());
        if ($contact) {
            return response()->json(['status' => 'OK', 'message' => 'Contact updated successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to update contact.'], 500);
    }

    public function destroy($id)
    {
        if ($this->contactService->delete($id)) {
            return response()->json(['status' => 'OK', 'message' => 'Contact deleted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to delete contact.'], 500);
    }

    public function show($id)
    {
        if ($contact = $this->contactService->getById($id)) {
            return response()->json(['status' => 'OK', 'data' => $contact], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Contact not found.'], 404);
    }
}
