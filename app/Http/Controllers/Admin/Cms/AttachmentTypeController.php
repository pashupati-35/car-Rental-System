<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttachmentTypeController extends Controller
{
    public function index()
    {
        return response()->json(['status' => 'OK', 'data' => []]);
    }

    public function store(Request $request)
    {
        return response()->json(['status' => 'OK', 'message' => 'Attachment type created.']);
    }

    public function show($id)
    {
        return response()->json(['status' => 'OK', 'data' => null]);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['status' => 'OK', 'message' => 'Attachment type updated.']);
    }

    public function destroy($id)
    {
        return response()->json(['status' => 'OK', 'message' => 'Attachment type deleted.']);
    }
}
