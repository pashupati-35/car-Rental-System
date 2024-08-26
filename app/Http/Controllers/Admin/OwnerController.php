<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingCar;
use App\Models\Car;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OwnerController extends Controller
{
public function index()
{
$owners = Owner::all();
return view('admin.auth.manage_owner', compact('owners'));
}

public function edit($id)
{
$owner = Owner::findOrFail($id);
return view('admin.auth.owner_edit', compact('owner'));
}

public function view($id)
{
$owner = Owner::findOrFail($id);
return view('admin.auth.owner_view', compact('owner'));
}

public function destroy($id)
{
$owner = Owner::findOrFail($id);
$owner->delete();

return redirect()->route('admin.owner.index')->with('status', 'Owner deleted successfully.');
}
    public function update(Request $request, $id)
    {
        $owner = Owner::findOrFail($id);
        $owner->update($request->all());

        return redirect()->route('admin.owner.index')->with('status', 'Owner updated successfully.');
    }

    public function dashboard()
    {
        $owner = Auth::guard('owner')->id();

        if (!$owner) {
            abort(403, 'Unauthorized action.');
        }

        Log::info('Authenticated owner:', ['owner_id' => $owner]);

        $cars = Car::where('owner_id', $owner)->get();

        if ($cars->isEmpty()) {
            Log::info('No cars found for owner:', ['owner_id' => $owner]);
        }

        return view('owner.dashboard', compact('cars'));
    }
    public function search(Request $request)
    {
        $query = $request->input('query');
        return redirect()->route('owner.dashboard')->with('success','search completed');
    }

    public function VerifiedCars()
    {
        $owner = Auth::guard('owner')->id();
        $cars = Car::where('status', 'verified')
            ->where('owner_id', $owner)
            ->get();

        return view('owner.auth.verified_car', compact('cars'));
    }


}
