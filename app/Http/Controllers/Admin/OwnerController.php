<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Car\StoreCarRequest;
use App\Http\Requests\Admin\Car\UpdateCarRequest;
use App\Http\Requests\Admin\Driver\StoreDriverRequest;
use App\Http\Requests\Admin\Driver\UpdateDriverRequest;
use App\Http\Requests\Admin\Owner\UpdateOwnerRequest;
use App\Services\BookingService;
use App\Services\CarService;
use App\Services\DriverService;
use App\Services\OwnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class OwnerController extends Controller
{
    public function __construct(
        protected OwnerService $ownerService,
        protected CarService $carService,
        protected DriverService $driverService,
        protected BookingService $bookingService,
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
        return Inertia::render('admin/OwnersList', [
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
        $data = $this->ownerService->getOwnerHubDetails((int) $id);

        return Inertia::render('admin/OwnerDetails', $data);
    }

    public function edit($id)
    {
        $owner = $this->ownerService->getOwnerById((int) $id);
        return view('admin.auth.owner_edit', compact('owner'));
    }

    public function view($id)
    {
        return $this->show(request(), $id);
    }

    public function destroy($id)
    {
        $this->ownerService->deleteOwner((int) $id);

        if (request()->wantsJson()) {
            return response()->json(['status' => 'OK', 'message' => 'Owner deleted successfully.']);
        }

        return redirect()->route('admin.owner.index')->with('status', 'Owner deleted successfully.');
    }

    public function update(UpdateOwnerRequest $request, $id)
    {
        $validated = $request->validated();

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $owner = $this->ownerService->updateOwner((int) $id, $validated);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'OK', 'message' => 'Owner updated successfully.', 'data' => $owner]);
        }

        return redirect()->back()->with('status', 'Owner updated successfully.');
    }

    /**
     * Store a car for this specific owner.
     */
    public function storeCar(StoreCarRequest $request, $ownerId)
    {
        $validated = $request->validated();

        $this->carService->createCarForOwner(
            (int) $ownerId,
            $validated,
            $request->file('car_photo'),
            $request->file('blue_book_photo')
        );

        if ($request->wantsJson()) {
            return response()->json(['status' => 'OK', 'message' => 'Car added to fleet.']);
        }

        return redirect()->back()->with('status', 'Vehicle successfully added to owner fleet.');
    }

    /**
     * Update car for this specific owner.
     */
    public function updateCar(UpdateCarRequest $request, $ownerId, $carId)
    {
        $validated = $request->validated();

        $this->carService->updateCarForOwner(
            (int) $ownerId,
            (int) $carId,
            $validated,
            $request->file('car_photo'),
            $request->file('blue_book_photo')
        );

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
        $this->carService->deleteCarForOwner((int) $ownerId, (int) $carId);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'OK', 'message' => 'Car deleted.']);
        }

        return redirect()->back()->with('status', 'Vehicle deleted from owner fleet.');
    }

    /**
     * Store driver for this specific owner.
     */
    public function storeDriver(StoreDriverRequest $request, $ownerId)
    {
        $validated = $request->validated();

        $this->driverService->createDriverForOwner(
            (int) $ownerId,
            $validated,
            $request->file('photo')
        );

        if ($request->wantsJson()) {
            return response()->json(['status' => 'OK', 'message' => 'Driver added.']);
        }

        return redirect()->back()->with('status', 'Driver registered for owner successfully.');
    }

    /**
     * Update driver for this specific owner.
     */
    public function updateDriver(UpdateDriverRequest $request, $ownerId, $driverId)
    {
        $validated = $request->validated();

        $this->driverService->updateDriverForOwner(
            (int) $ownerId,
            (int) $driverId,
            $validated,
            $request->file('photo')
        );

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
        $this->driverService->deleteDriverForOwner((int) $ownerId, (int) $driverId);

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

        $cars = $this->carService->getCarsByOwner($owner);

        if ($cars->isEmpty()) {
            Log::info('No cars found for owner:', ['owner_id' => $owner]);
        }

        return view('owner.dashboard', compact('cars'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        return redirect()->route('owner.dashboard')->with('success', 'search completed');
    }

    public function VerifiedCars()
    {
        $owner = Auth::guard('owner')->id();
        $cars = $this->carService->getVerifiedCars((int) $owner);
        return view('owner.dashboard', compact('cars'));
    }
}
