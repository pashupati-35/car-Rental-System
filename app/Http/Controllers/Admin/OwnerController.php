<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingCar;
use App\Models\Car;
use App\Models\Driver;
use App\Models\Owner;
use App\Services\OwnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class OwnerController extends Controller
{
    public function __construct(
        protected OwnerService $ownerService,
    ) {}

    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = (int) $request->input('per_page', 10);

        $owners = $this->ownerService->getAdminOwners(['search' => $search], $perPage);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'data' => $owners,
            ]);
        }
        return \Inertia\Inertia::render('admin/OwnersList', [
            'owners' => $owners,
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
            ]
        ]);
    }

    /**
     * Dedicated Owner Hub / Dashboard for Admin to inspect and manage everything.
     */
    public function show(Request $request, $id)
    {
        $owner = Owner::withCount(['cars', 'drivers'])->findOrFail($id);

        // Cars belonging to this owner
        $cars = Car::with(['driver', 'booking.customer'])
            ->where('owner_id', $id)
            ->latest('id')
            ->get();

        // Drivers assigned to this owner
        $drivers = Driver::withCount('cars')
            ->where('owner_id', $id)
            ->latest('id')
            ->get();

        // All available drivers for assignment dropdown (either this owner or system)
        $availableDrivers = Driver::where(function ($q) use ($id) {
            $q->where('owner_id', $id)->orWhereNull('owner_id');
        })->where('status', 'active')->get();

        // Bookings across this owner's cars
        $carIds = $cars->pluck('id')->toArray();
        $bookings = BookingCar::with(['customer', 'car'])
            ->whereIn('car_id', $carIds)
            ->latest('id')
            ->get();

        // Summary KPI statistics
        $stats = [
            'total_cars' => $cars->count(),
            'verified_cars' => $cars->whereIn('status', ['verified', 'available'])->count(),
            'pending_cars' => $cars->where('status', 'pending')->count(),
            'rejected_cars' => $cars->where('status', 'rejected')->count(),
            'total_drivers' => $drivers->count(),
            'total_bookings' => $bookings->count(),
            'confirmed_bookings' => $bookings->whereIn('status', ['confirm', 'confirmed', 'completed'])->count(),
            'total_revenue' => $bookings->whereIn('status', ['confirm', 'confirmed', 'completed'])->sum('total_price'),
        ];

        return \Inertia\Inertia::render('admin/OwnerDetails', [
            'owner' => $owner,
            'cars' => $cars,
            'drivers' => $drivers,
            'availableDrivers' => $availableDrivers,
            'bookings' => $bookings,
            'stats' => $stats,
        ]);
    }

    public function edit($id)
    {
        $owner = Owner::findOrFail($id);
        return view('admin.auth.owner_edit', compact('owner'));
    }

    public function view($id)
    {
        return $this->show(request(), $id);
    }

    public function destroy($id)
    {
        $owner = Owner::findOrFail($id);
        $owner->delete();

        if (request()->wantsJson()) {
            return response()->json(['status' => 'OK', 'message' => 'Owner deleted successfully.']);
        }

        return redirect()->route('admin.owner.index')->with('status', 'Owner deleted successfully.');
    }

    public function update(Request $request, $id)
    {
        $owner = Owner::findOrFail($id);
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:owners,email,' . $id,
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $owner->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'OK', 'message' => 'Owner updated successfully.', 'data' => $owner]);
        }

        return redirect()->back()->with('status', 'Owner updated successfully.');
    }

    /**
     * Store a car for this specific owner.
     */
    public function storeCar(Request $request, $ownerId)
    {
        $owner = Owner::findOrFail($ownerId);

        $validated = $request->validate([
            'car_name' => 'required|string|max:255',
            'car_model' => 'required|string|max:255',
            'car_number' => 'required|string|max:100',
            'number_of_seats' => 'nullable|numeric|min:1',
            'car_price_per_day' => 'required|numeric|min:0',
            'car_price_per_km' => 'nullable|numeric|min:0',
            'driver_id' => 'nullable|exists:drivers,id',
            'status' => 'nullable|string',
            'car_photo' => 'nullable|image|max:5120',
            'blue_book_photo' => 'nullable|image|max:5120',
        ]);

        $validated['owner_id'] = $owner->id;
        $validated['status'] = $validated['status'] ?? 'verified';
        $validated['available'] = 1;

        if ($request->hasFile('car_photo')) {
            $file = $request->file('car_photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/cars'), $fileName);
            $validated['car_photo'] = 'uploads/cars/' . $fileName;
        }

        if ($request->hasFile('blue_book_photo')) {
            $file = $request->file('blue_book_photo');
            $fileName = 'bluebook_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/bluebooks'), $fileName);
            $validated['blue_book_photo'] = 'uploads/bluebooks/' . $fileName;
        }

        Car::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'OK', 'message' => 'Car added to fleet.']);
        }

        return redirect()->back()->with('status', 'Vehicle successfully added to owner fleet.');
    }

    /**
     * Update car for this specific owner.
     */
    public function updateCar(Request $request, $ownerId, $carId)
    {
        $car = Car::where('owner_id', $ownerId)->findOrFail($carId);

        $validated = $request->validate([
            'car_name' => 'required|string|max:255',
            'car_model' => 'required|string|max:255',
            'car_number' => 'required|string|max:100',
            'number_of_seats' => 'nullable|numeric|min:1',
            'car_price_per_day' => 'required|numeric|min:0',
            'car_price_per_km' => 'nullable|numeric|min:0',
            'driver_id' => 'nullable|exists:drivers,id',
            'status' => 'nullable|string',
            'car_photo' => 'nullable|image|max:5120',
            'blue_book_photo' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('car_photo')) {
            $file = $request->file('car_photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/cars'), $fileName);
            $validated['car_photo'] = 'uploads/cars/' . $fileName;
        }

        if ($request->hasFile('blue_book_photo')) {
            $file = $request->file('blue_book_photo');
            $fileName = 'bluebook_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/bluebooks'), $fileName);
            $validated['blue_book_photo'] = 'uploads/bluebooks/' . $fileName;
        }

        $car->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'OK', 'message' => 'Car updated.']);
        }

        return redirect()->back()->with('status', 'Vehicle updated successfully.');
    }

    /**
     * Delete car for this specific owner.
     */
    public function destroyCar(Request $request, $ownerId, $carId)
    {
        $car = Car::where('owner_id', $ownerId)->findOrFail($carId);
        $car->delete();

        if ($request->wantsJson()) {
            return response()->json(['status' => 'OK', 'message' => 'Car deleted.']);
        }

        return redirect()->back()->with('status', 'Vehicle deleted from owner fleet.');
    }

    /**
     * Store driver for this specific owner.
     */
    public function storeDriver(Request $request, $ownerId)
    {
        $owner = Owner::findOrFail($ownerId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'license_number' => 'required|string|max:100',
            'experience_years' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:5120',
        ]);

        $validated['owner_id'] = $owner->id;
        $validated['status'] = $validated['status'] ?? 'active';

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/drivers'), $fileName);
            $validated['photo'] = 'uploads/drivers/' . $fileName;
        }

        Driver::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'OK', 'message' => 'Driver added.']);
        }

        return redirect()->back()->with('status', 'Driver registered for owner successfully.');
    }

    /**
     * Update driver for this specific owner.
     */
    public function updateDriver(Request $request, $ownerId, $driverId)
    {
        $driver = Driver::where('owner_id', $ownerId)->findOrFail($driverId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'license_number' => 'required|string|max:100',
            'experience_years' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/drivers'), $fileName);
            $validated['photo'] = 'uploads/drivers/' . $fileName;
        }

        $driver->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'OK', 'message' => 'Driver updated.']);
        }

        return redirect()->back()->with('status', 'Driver record updated.');
    }

    /**
     * Delete driver for this specific owner.
     */
    public function destroyDriver(Request $request, $ownerId, $driverId)
    {
        $driver = Driver::where('owner_id', $ownerId)->findOrFail($driverId);
        $driver->delete();

        if ($request->wantsJson()) {
            return response()->json(['status' => 'OK', 'message' => 'Driver deleted.']);
        }

        return redirect()->back()->with('status', 'Driver deleted.');
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
        return view('owner.dashboard', compact('cars'));
    }
}
