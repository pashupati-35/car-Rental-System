<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\Driver\StoreDriverRequest;
use App\Http\Requests\Owner\Driver\UpdateDriverRequest;
use App\Services\DriverService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DriverController extends Controller
{
    public function __construct(
        protected DriverService $driverService
    ) {}

    /**
     * Display a listing of drivers for the authenticated owner.
     */
    public function index(Request $request)
    {
        $ownerId = Auth::guard('owner')->id();
        $drivers = $this->driverService->getDriversByOwner($ownerId);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $drivers,
            ]);
        }

        return Inertia::render('owner/drivers/Index', [
            'drivers' => $drivers,
        ]);
    }

    /**
     * Store a newly created driver in storage.
     */
    public function store(StoreDriverRequest $request)
    {
        $ownerId = Auth::guard('owner')->id();
        $validated = $request->validated();

        $driver = $this->driverService->createDriverForOwner(
            $ownerId,
            $validated,
            $request->file('photo'),
            $request->file('license_photo')
        );

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Driver created successfully.',
                'data' => $driver,
            ], 201);
        }

        return redirect()->back()->with('success', 'Driver created successfully.');
    }

    /**
     * Update the specified driver in storage.
     */
    public function update(UpdateDriverRequest $request, $id)
    {
        $ownerId = Auth::guard('owner')->id();
        $validated = $request->validated();

        $driver = $this->driverService->updateDriverForOwner(
            $ownerId,
            (int) $id,
            $validated,
            $request->file('photo'),
            $request->file('license_photo')
        );

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Driver updated successfully.',
                'data' => $driver,
            ]);
        }

        return redirect()->back()->with('success', 'Driver updated successfully.');
    }

    /**
     * Remove the specified driver from storage.
     */
    public function destroy(Request $request, $id)
    {
        $ownerId = Auth::guard('owner')->id();
        $this->driverService->deleteDriverForOwner($ownerId, (int) $id);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Driver deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Driver deleted successfully.');
    }
}
