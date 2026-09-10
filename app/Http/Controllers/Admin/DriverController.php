<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Driver\StoreDriverRequest;
use App\Http\Requests\Admin\Driver\UpdateDriverRequest;
use App\Services\DriverService;
use App\Services\OwnerService;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function __construct(
        protected DriverService $driverService,
        protected OwnerService $ownerService,
    ) {}

    /**
     * Get list of drivers with filters (JSON response).
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $search = $request->input('search');
        $ownerId = $request->input('owner_id');
        $status = $request->input('status');

        $filters = [
            'search' => $search,
            'owner_id' => $ownerId,
            'status' => $status,
        ];

        $drivers = $this->driverService->getAdminDrivers($filters, $perPage);
        $owners = $this->ownerService->getOwnersDropdown();
        $driverCounts = $this->driverService->getDriverStatusCounts();

        return response()->json([
            'status' => 'success',
            'drivers' => $drivers,
            'owners' => $owners,
            'counts' => $driverCounts,
            'filters' => [
                'search' => $search ?? '',
                'owner_id' => $ownerId ?? '',
                'status' => $status ?? '',
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Get single driver details.
     */
    public function show($id)
    {
        $driver = $this->driverService->getDriverById((int) $id);

        return response()->json([
            'status' => 'success',
            'data' => $driver,
        ]);
    }

    /**
     * Store new driver.
     */
    public function store(StoreDriverRequest $request)
    {
        $driver = $this->driverService->createDriver(
            $request->validated(),
            $request->file('photo'),
            $request->file('license_photo'),
        );

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Driver added successfully.',
                'data' => $driver,
            ], 201);
        }

        return redirect()->route('admin.drivers')->with('success', 'Driver added to global directory.');
    }

    /**
     * Update driver details.
     */
    public function update(UpdateDriverRequest $request, $id)
    {
        $driver = $this->driverService->updateDriver(
            (int) $id,
            $request->validated(),
            $request->file('photo'),
            $request->file('license_photo'),
        );

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Driver updated successfully.',
                'data' => $driver,
            ]);
        }

        return redirect()->route('admin.drivers')->with('success', 'Driver record updated.');
    }

    /**
     * Delete driver.
     */
    public function destroy($id)
    {
        $this->driverService->deleteDriver((int) $id);

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Driver deleted successfully.',
            ]);
        }

        return redirect()->route('admin.drivers')->with('success', 'Driver deleted successfully.');
    }
}
