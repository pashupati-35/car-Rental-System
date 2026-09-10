<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Car\StoreCarRequest;
use App\Http\Requests\Admin\Car\UpdateCarRequest;
use App\Http\Requests\Admin\Driver\StoreDriverRequest;
use App\Http\Requests\Admin\Driver\UpdateDriverRequest;
use App\Http\Requests\Admin\Owner\StoreOwnerRequest;
use App\Http\Requests\Admin\Owner\UpdateOwnerRequest;
use App\Services\Auth\PasswordResetService;
use App\Services\BookingService;
use App\Services\CarService;
use App\Services\DriverService;
use App\Services\OwnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OwnerController extends Controller
{
    public function __construct(
        protected OwnerService $ownerService,
        protected CarService $carService,
        protected DriverService $driverService,
        protected BookingService $bookingService,
    ) {}

    /**
     * Get paginated owners list (JSON response).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = (int) $request->input('per_page', 10);

        $owners = $this->ownerService->getAdminOwners(['search' => $search], $perPage);

        return response()->json([
            'status' => 'success',
            'owners' => $owners,
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Register a new fleet owner by Admin.
     */
    public function store(StoreOwnerRequest $request)
    {
        $validated = $request->validated();
        $validated['admin_id'] = Auth::guard('admin')->id();
        if (empty($validated['password'])) {
            $validated['password'] = Hash::make(Str::random(16));
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $owner = $this->ownerService->createOwner($validated, $request->file('image'));
        $mailResult = PasswordResetService::sendResetLink($owner->email, 'owner');

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Fleet Owner registered. A password setup email has been dispatched.',
                'data' => $owner,
                'reset_url' => $mailResult['reset_url'] ?? null,
            ], 201);
        }

        return redirect()->back()->with('success', 'Fleet Owner registered! A password setup email has been sent.');
    }

    /**
     * Get owner details / hub data (JSON response).
     */
    public function show(Request $request, $id)
    {
        $data = $this->ownerService->getOwnerHubDetails((int) $id);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    /**
     * Admin login as fleet owner.
     */
    public function loginAs(Request $request, $id)
    {
        $owner = $this->ownerService->getOwnerById((int) $id);

        if (!$owner) {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Fleet owner not found.'], 404);
            }
            return redirect()->back()->with('error', 'Fleet owner not found.');
        }

        Auth::guard('owner')->loginUsingId($owner->id);
        $request->session()->put('admin_impersonating', true);
        $request->session()->put('impersonated_by_admin', Auth::guard('admin')->id());
        $request->session()->save();

        $host = $request->getHost();
        $mainHost = preg_replace('/^portal\./i', '', $host);
        $scheme = $request->getScheme();
        $port = $request->getPort();
        $portSuffix = ($port && !in_array($port, [80, 443])) ? ':' . $port : '';
        $redirectUrl = $scheme . '://' . $mainHost . $portSuffix . '/owner/dashboard';

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Logged in as fleet owner ' . ($owner->full_name ?? $owner->email),
                'redirect_url' => $redirectUrl,
            ]);
        }

        return redirect()->away($redirectUrl);
    }

    /**
     * Update owner profile.
     */
    public function update(UpdateOwnerRequest $request, $id)
    {
        $validated = $request->validated();

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $owner = $this->ownerService->updateOwner((int) $id, $validated, $request->file('image'));

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Owner updated successfully.',
                'data' => $owner,
            ]);
        }

        return redirect()->back()->with('success', 'Owner updated successfully.');
    }

    /**
     * Delete owner.
     */
    public function destroy($id)
    {
        $this->ownerService->deleteOwner((int) $id);

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Owner deleted successfully.',
            ]);
        }

        return redirect()->route('admin.owners')->with('success', 'Owner deleted successfully.');
    }

    /**
     * Store a car for this specific owner.
     */
    public function storeCar(StoreCarRequest $request, $ownerId)
    {
        $validated = $request->validated();

        $car = $this->carService->createCarForOwner(
            (int) $ownerId,
            $validated,
            $request->file('car_photo'),
            $request->file('blue_book_photo')
        );

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Car added to fleet.',
                'data' => $car,
            ], 201);
        }

        return redirect()->back()->with('success', 'Vehicle successfully added to owner fleet.');
    }

    /**
     * Update car for this specific owner.
     */
    public function updateCar(UpdateCarRequest $request, $ownerId, $carId)
    {
        $validated = $request->validated();

        $car = $this->carService->updateCarForOwner(
            (int) $ownerId,
            (int) $carId,
            $validated,
            $request->file('car_photo'),
            $request->file('blue_book_photo')
        );

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Car updated.',
                'data' => $car,
            ]);
        }

        return redirect()->back()->with('success', 'Vehicle updated successfully.');
    }

    /**
     * Delete car for this specific owner.
     */
    public function destroyCar(Request $request, $ownerId, $carId)
    {
        $this->carService->deleteCarForOwner((int) $ownerId, (int) $carId);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Car deleted.',
            ]);
        }

        return redirect()->back()->with('success', 'Vehicle deleted from owner fleet.');
    }

    /**
     * Store driver for this specific owner.
     */
    public function storeDriver(StoreDriverRequest $request, $ownerId)
    {
        $validated = $request->validated();

        $driver = $this->driverService->createDriverForOwner(
            (int) $ownerId,
            $validated,
            $request->file('photo'),
            $request->file('license_photo')
        );

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Driver added.',
                'data' => $driver,
            ], 201);
        }

        return redirect()->back()->with('success', 'Driver registered for owner successfully.');
    }

    /**
     * Update driver for this specific owner.
     */
    public function updateDriver(UpdateDriverRequest $request, $ownerId, $driverId)
    {
        $validated = $request->validated();

        $driver = $this->driverService->updateDriverForOwner(
            (int) $ownerId,
            (int) $driverId,
            $validated,
            $request->file('photo'),
            $request->file('license_photo')
        );

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Driver updated.',
                'data' => $driver,
            ]);
        }

        return redirect()->back()->with('success', 'Driver record updated.');
    }

    /**
     * Delete driver for this specific owner.
     */
    public function destroyDriver(Request $request, $ownerId, $driverId)
    {
        $this->driverService->deleteDriverForOwner((int) $ownerId, (int) $driverId);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Driver deleted.',
            ]);
        }

        return redirect()->back()->with('success', 'Driver deleted.');
    }
}
