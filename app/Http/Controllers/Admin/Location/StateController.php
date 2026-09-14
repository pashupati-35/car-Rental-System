<?php

namespace App\Http\Controllers\Admin\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function index()
    {
        return response()->json(['status' => 'OK', 'data' => []]);
    }

    public function store(Request $request)
    {
        return response()->json(['status' => 'OK', 'message' => 'State created.']);
    }

    public function show($id)
    {
        return response()->json(['status' => 'OK', 'data' => null]);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['status' => 'OK', 'message' => 'State updated.']);
    }

    public function destroy($id)
    {
        return response()->json(['status' => 'OK', 'message' => 'State deleted.']);
    }
}
