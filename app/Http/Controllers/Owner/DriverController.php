<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DriverController extends Controller
{
    /**
     * Display a listing of drivers for the authenticated owner.
     */
    public function index(Request $request)
    {
        $ownerId = Auth::guard('owner')->id();
        $drivers = Driver::with('cars')
            ->where('owner_id', $ownerId)
            ->latest()
            ->get();

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
    public function store(Request $request)
    {
        $ownerId = Auth::guard('owner')->id();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'license_number' => 'required|string|max:50',
            'experience_years' => 'required|string|max:10',
            'photo' => 'nullable|image|max:2048',
            'license_photo' => 'nullable|image|max:2048',
            'status' => 'nullable|in:active,inactive,on_trip',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_driver_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/drivers'), $fileName);
            $photoPath = 'uploads/drivers/' . $fileName;
        }

        $licensePhotoPath = null;
        if ($request->hasFile('license_photo')) {
            $file = $request->file('license_photo');
            $fileName = time() . '_license_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/drivers'), $fileName);
            $licensePhotoPath = 'uploads/drivers/' . $fileName;
        }

        $driver = Driver::create([
            'owner_id' => $ownerId,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'license_number' => $validated['license_number'],
            'experience_years' => $validated['experience_years'],
            'photo' => $photoPath,
            'license_photo' => $licensePhotoPath,
            'status' => $validated['status'] ?? 'active',
        ]);

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
    public function update(Request $request, $id)
    {
        $ownerId = Auth::guard('owner')->id();
        $driver = Driver::where('owner_id', $ownerId)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'license_number' => 'required|string|max:50',
            'experience_years' => 'required|string|max:10',
            'photo' => 'nullable|image|max:2048',
            'license_photo' => 'nullable|image|max:2048',
            'status' => 'nullable|in:active,inactive,on_trip',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_driver_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/drivers'), $fileName);
            $validated['photo'] = 'uploads/drivers/' . $fileName;
        }

        if ($request->hasFile('license_photo')) {
            $file = $request->file('license_photo');
            $fileName = time() . '_license_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/drivers'), $fileName);
            $validated['license_photo'] = 'uploads/drivers/' . $fileName;
        }

        $driver->update($validated);

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
        $driver = Driver::where('owner_id', $ownerId)->findOrFail($id);
        $driver->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Driver deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Driver deleted successfully.');
    }
}
