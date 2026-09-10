<?php

namespace App\Http\Controllers\Admin\Cms\ContactUs;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactUs\ContactUsRequest;
use App\Services\Cms\ContactUs\ContactUsService;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    public function __construct(protected ContactUsService $contact) {}

    public function index(Request $request)
    {
        return $this->contact->paginate($request->all(), $request->per_pages ?? 25);
    }

    public function store(ContactUsRequest $request)
    {
        $contact = $this->contact->store($request->validated());
        if ($contact) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function update(ContactUsRequest $request, $id)
    {
        $contact = $this->contact->update($id, $request->validated());
        if ($contact) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function destroy($id)
    {
        if ($this->contact->delete($id)) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function show($id)
    {
        if ($contact = $this->contact->getById($id)) {
            return response(['status' => 'OK', 'contact' => $contact], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    // public function exportList(Request $request)
    // {
    //     if (!empty(auth()->guard('web')->user())) {
    //         ob_end_clean();
    //         ob_start();
    //         return App\Http\Controllers\User\ContactUs\Excel::download(new DownloadEnqueryExport($request->all()), 'ResourseDownloadedList.xlsx');
    //     }
    // }
}
