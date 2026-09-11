<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Display the customer's profile form.
     */
    public function edit(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'user' => $customer,
            ]);
        }

        return Inertia::render('customer/Profile', [
            'user' => $customer,
        ]);
    }

    /**
     * Display the customer's security & MFA form.
     */
    public function security(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'user' => $customer,
            ]);
        }

        return Inertia::render('customer/Security', [
            'user' => $customer,
        ]);
    }

    /**
     * Update the customer's profile information.
     */
    public function update(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $validated = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email,'.$customer->id,
            'phone_number' => 'nullable|string|max:25',
            'mobile' => 'nullable|string|max:25',
            'phone' => 'nullable|string|max:25',
            'username' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'marital_status' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
            'citizenship_number' => 'nullable|string|max:100',
            'passport_number' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'designation' => 'nullable|string|max:100',
            'emergency_contact' => 'nullable|string|max:50',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_relationship' => 'nullable|string|max:100',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,bmp,heic,heif,avif|max:5120',
        ]);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            $uploadDir = public_path('uploads/customer');
            if (! file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileName = time().'_'.$image->getClientOriginalName();
            $image->move($uploadDir, $fileName);
            $validated['image'] = 'uploads/customer/'.$fileName;
        } elseif ($request->boolean('remove_image')) {
            $validated['image'] = null;
        }

        // Sync display name if first/last name are provided
        if (! empty($validated['first_name']) || ! empty($validated['last_name'])) {
            $validated['name'] = trim(($validated['first_name'] ?? '').' '.($validated['middle_name'] ?? '').' '.($validated['last_name'] ?? ''));
        } elseif (! empty($validated['name']) && empty($validated['first_name'])) {
            $parts = explode(' ', trim($validated['name']));
            $validated['first_name'] = $parts[0] ?? '';
            $validated['last_name'] = count($parts) > 1 ? end($parts) : '';
        }

        $customer->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'OK',
                'message' => 'Profile updated successfully.',
                'user' => $customer->fresh(),
            ]);
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete the customer's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password:customer'],
        ]);

        $customer = Auth::guard('customer')->user();

        Auth::guard('customer')->logout();
        $customer->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/customer/login');
    }
}
