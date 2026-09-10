<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\Admin\BookingStatusNotificationMail;
use App\Mail\Admin\CarStatusNotificationMail;
use App\Models\Admin;
use App\Models\BookingCar;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Owner;
use App\Models\Payment;
use App\Services\Auth\PasswordResetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminController extends Controller
{
    /**
     * Paginated Fleet Cars list with filters.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Car::with(['owner', 'driver']);

        if (filled($status) && $status !== 'all') {
            if ($status === 'verified') {
                $query->whereIn('status', ['verified', 'available']);
            } elseif ($status === 'pending') {
                $query->where(function ($q) {
                    $q->where('status', 'pending')->orWhereNull('status');
                });
            } else {
                $query->where('status', $status);
            }
        }

        if (filled($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('car_name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('car_model', 'like', "%{$search}%")
                  ->orWhere('car_number', 'like', "%{$search}%")
                  ->orWhere('plate_number', 'like', "%{$search}%")
                  ->orWhereHas('owner', function ($oq) use ($search) {
                      $oq->where('full_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $cars = $query->latest('id')->paginate($perPage)->withQueryString();

        return Inertia::render('admin/CarsList', [
            'cars' => $cars,
            'filters' => [
                'status' => $status ?? 'all',
                'search' => $search ?? '',
                'per_page' => $perPage,
            ],
        ]);
    }

    public function show($id)
    {
        $car = Car::with(['owner', 'driver'])->findOrFail($id);
        return Inertia::render('cars/Show', [
            'car' => $car,
        ]);
    }

    /**
     * Approve vehicle and send email notification to owner.
     */
    public function verifyCar(Car $car)
    {
        $car->update([
            'status' => 'verified',
            'available' => 'yes',
        ]);

        // Send email to the vehicle owner
        if ($car->owner && filled($car->owner->email)) {
            try {
                Mail::to($car->owner->email)->send(new CarStatusNotificationMail($car, 'verified'));
            } catch (\Throwable $e) {
                Log::warning('Failed to send car verification email to owner: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Car has been verified, listed, and an approval notification has been emailed to the owner.');
    }

    /**
     * Reject vehicle and send email notification to owner.
     */
    public function rejectCar(Car $car)
    {
        $car->update([
            'status' => 'rejected',
            'available' => 'no',
        ]);

        // Send email to the vehicle owner
        if ($car->owner && filled($car->owner->email)) {
            try {
                Mail::to($car->owner->email)->send(new CarStatusNotificationMail($car, 'rejected'));
            } catch (\Throwable $e) {
                Log::warning('Failed to send car rejection email to owner: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Car has been rejected and an update email has been sent to the owner.');
    }

    public function destroyCar($id)
    {
        $car = Car::findOrFail($id);
        $car->delete();

        return redirect()->back()->with('success', 'Vehicle removed from system fleet.');
    }

    /**
     * Driver directory & CRUD for Admin with pagination.
     */
    public function viewDrivers(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Driver::with(['owner', 'cars']);

        if (filled($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('license_number', 'like', "%{$search}%");
            });
        }

        $drivers = $query->latest('id')->paginate($perPage)->withQueryString();
        $owners = Owner::select('id', 'full_name', 'email')->get();

        return Inertia::render('admin/DriversList', [
            'drivers' => $drivers,
            'owners' => $owners,
            'filters' => [
                'search' => $search ?? '',
                'per_page' => $perPage,
            ],
        ]);
    }

    public function storeDriver(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'license_number' => 'required|string|max:100',
            'experience_years' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            'owner_id' => 'nullable|exists:owners,id',
            'address' => 'nullable|string|max:255',
        ]);

        $driver = Driver::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Driver added successfully.', 'data' => $driver]);
        }

        return redirect()->route('admin.drivers')->with('success', 'Driver added to global directory.');
    }

    public function updateDriver(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'license_number' => 'required|string|max:100',
            'experience_years' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            'owner_id' => 'nullable|exists:owners,id',
            'address' => 'nullable|string|max:255',
        ]);

        $driver->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Driver updated successfully.', 'data' => $driver]);
        }

        return redirect()->route('admin.drivers')->with('success', 'Driver record updated.');
    }

    public function destroyDriver($id)
    {
        $driver = Driver::findOrFail($id);
        $driver->delete();

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Driver deleted.']);
        }

        return redirect()->route('admin.drivers')->with('success', 'Driver deleted successfully.');
    }

    /**
     * Paginated Customer directory for Admin.
     */
    public function viewCustomers(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Customer::withCount('bookings');

        if (filled($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest('id')->paginate($perPage)->withQueryString();

        return Inertia::render('admin/CustomersList', [
            'customers' => $customers,
            'filters' => [
                'search' => $search ?? '',
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Admin creates a customer and sends a password reset/setup email.
     */
    public function createCustomer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'gender' => 'nullable|string',
        ]);

        $randomPassword = Str::random(16);

        $customer = Customer::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'address' => $validated['address'],
            'gender' => $validated['gender'] ?? 'male',
            'password' => Hash::make($randomPassword),
            'admin_id' => Auth::guard('admin')->id(),
        ]);

        // Send reset password / setup email
        $mailResult = PasswordResetService::sendResetLink($customer->email, 'customer');

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Customer created successfully. A password setup email has been dispatched.',
                'data' => $customer,
                'reset_url' => $mailResult['reset_url'] ?? null,
            ], 201);
        }

        return redirect()->route('admin.customers')->with('success', 'Customer created successfully! A password setup email has been sent.');
    }

    public function updateCustomer(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email,' . $id,
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'gender' => 'nullable|string',
        ]);

        $customer->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Customer updated.', 'data' => $customer]);
        }

        return redirect()->route('admin.customers')->with('success', 'Customer details updated.');
    }

    /**
     * Admin creates an owner and sends a password reset/setup email.
     */
    public function createOwner(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:owners,email',
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'gender' => 'nullable|string',
        ]);

        $randomPassword = Str::random(16);

        $owner = Owner::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'contact_number' => $validated['contact_number'],
            'address' => $validated['address'],
            'gender' => $validated['gender'] ?? 'male',
            'password' => Hash::make($randomPassword),
            'admin_id' => Auth::guard('admin')->id(),
        ]);

        // Send reset password / setup email
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

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Customer deleted.']);
        }

        return redirect()->route('admin.customers')->with('success', 'Customer deleted successfully.');
    }

    /**
     * Dedicated Customer Hub / Dashboard for Admin.
     */
    public function showCustomer(Request $request, $id)
    {
        $customer = Customer::withCount('bookings')->findOrFail($id);

        $bookings = BookingCar::with(['car.driver', 'car.owner', 'payment'])
            ->where('customer_id', $id)
            ->latest('id')
            ->get();

        $payments = Payment::with(['car', 'booking'])
            ->where('customer_id', $id)
            ->latest('id')
            ->get();

        $availableCars = Car::whereIn('status', ['verified', 'available'])->get();

        $stats = [
            'total_bookings' => $bookings->count(),
            'confirmed_bookings' => $bookings->whereIn('status', ['confirm', 'confirmed'])->count(),
            'completed_bookings' => $bookings->where('status', 'completed')->count(),
            'cancelled_bookings' => $bookings->whereIn('status', ['cancel', 'cancelled'])->count(),
            'pending_bookings' => $bookings->where('status', 'pending')->count(),
            'total_spent' => $bookings->whereIn('status', ['confirm', 'confirmed', 'completed'])->sum('total_price'),
            'total_payments' => $payments->sum('amount'),
        ];

        return Inertia::render('admin/CustomerDetails', [
            'customer' => $customer,
            'bookings' => $bookings,
            'payments' => $payments,
            'availableCars' => $availableCars,
            'stats' => $stats,
        ]);
    }

    public function storeCustomerBooking(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'pick_up_date' => 'required|date',
            'last_date' => 'required|date|after_or_equal:pick_up_date',
            'pickup_location' => 'required|string|max:255',
            'drop_location' => 'required|string|max:255',
            'total_price' => 'required|numeric|min:0',
            'status' => 'nullable|string',
            'purpose' => 'nullable|string',
        ]);

        $booking = BookingCar::create([
            'customer_id' => $customer->id,
            'car_id' => $validated['car_id'],
            'pick_up_date' => $validated['pick_up_date'],
            'last_date' => $validated['last_date'],
            'pickup_location' => $validated['pickup_location'],
            'drop_location' => $validated['drop_location'],
            'total_price' => $validated['total_price'],
            'status' => $validated['status'] ?? 'confirm',
            'purpose' => $validated['purpose'] ?? 'Admin booked rental',
        ]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Booking created for customer.', 'data' => $booking]);
        }

        return redirect()->back()->with('success', 'Booking created successfully.');
    }

    public function updateCustomerBooking(Request $request, $id, $bookingId)
    {
        $booking = BookingCar::where('customer_id', $id)->findOrFail($bookingId);

        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'pick_up_date' => 'required|date',
            'last_date' => 'required|date|after_or_equal:pick_up_date',
            'pickup_location' => 'required|string|max:255',
            'drop_location' => 'required|string|max:255',
            'total_price' => 'required|numeric|min:0',
            'status' => 'nullable|string',
            'purpose' => 'nullable|string',
        ]);

        $booking->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Booking updated.', 'data' => $booking]);
        }

        return redirect()->back()->with('success', 'Booking updated successfully.');
    }

    public function destroyCustomerBooking($id, $bookingId)
    {
        $booking = BookingCar::where('customer_id', $id)->findOrFail($bookingId);
        $booking->delete();

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Booking deleted.']);
        }

        return redirect()->back()->with('success', 'Booking deleted successfully.');
    }

    public function storeCustomerPayment(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'booking_id' => 'required|exists:booking_car,id',
            'car_id' => 'required|exists:cars,id',
            'amount' => 'required|numeric|min:0',
            'card_number' => 'nullable|string',
            'expiry_date' => 'nullable|string',
        ]);

        $payment = Payment::create([
            'customer_id' => $customer->id,
            'booking_id' => $validated['booking_id'],
            'car_id' => $validated['car_id'],
            'amount' => $validated['amount'],
            'card_number' => $validated['card_number'] ?? '4242424242424242',
            'expiry_date' => $validated['expiry_date'] ?? '12/28',
            'cvv' => '123',
        ]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Payment recorded.', 'data' => $payment]);
        }

        return redirect()->back()->with('success', 'Payment recorded.');
    }

    public function destroyCustomerPayment($id, $paymentId)
    {
        $payment = Payment::where('customer_id', $id)->findOrFail($paymentId);
        $payment->delete();

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Payment record deleted.']);
        }

        return redirect()->back()->with('success', 'Payment deleted.');
    }

    /**
     * Paginated Booked Cars / Reservations for Admin.
     */
    public function viewBookings(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $status = $request->input('status');
        $search = $request->input('search');

        $query = BookingCar::with(['car.owner', 'customer', 'payment']);

        if (filled($status) && $status !== 'all') {
            if ($status === 'confirmed' || $status === 'confirm') {
                $query->whereIn('status', ['confirm', 'confirmed']);
            } elseif ($status === 'cancelled' || $status === 'cancel') {
                $query->whereIn('status', ['cancel', 'cancelled']);
            } else {
                $query->where('status', $status);
            }
        }

        if (filled($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('car', function ($carq) use ($search) {
                      $carq->where('car_name', 'like', "%{$search}%")
                           ->orWhere('car_model', 'like', "%{$search}%")
                           ->orWhere('car_number', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->latest('id')->paginate($perPage)->withQueryString();

        return Inertia::render('admin/BookedCars', [
            'bookedCars' => $bookings,
            'filters' => [
                'status' => $status ?? 'all',
                'search' => $search ?? '',
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Confirm booking and email customer.
     */
    public function confirmBooking($id)
    {
        $booking = BookingCar::with(['car', 'customer'])->findOrFail($id);
        $booking->update(['status' => 'confirm']);

        $recipientEmail = $booking->customer->email ?? $booking->email ?? null;
        if (filled($recipientEmail)) {
            try {
                Mail::to($recipientEmail)->send(new BookingStatusNotificationMail($booking, 'confirm'));
            } catch (\Throwable $e) {
                Log::warning('Failed to send booking confirmation email to customer: ' . $e->getMessage());
            }
        }

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Booking confirmed and notification dispatched.', 'data' => $booking]);
        }

        return redirect()->back()->with('success', 'Booking has been confirmed and a confirmation email has been dispatched to the customer.');
    }

    /**
     * Cancel booking and email customer.
     */
    public function cancelBooking($id)
    {
        $booking = BookingCar::with(['car', 'customer'])->findOrFail($id);
        $booking->update(['status' => 'cancel']);

        $recipientEmail = $booking->customer->email ?? $booking->email ?? null;
        if (filled($recipientEmail)) {
            try {
                Mail::to($recipientEmail)->send(new BookingStatusNotificationMail($booking, 'cancel'));
            } catch (\Throwable $e) {
                Log::warning('Failed to send booking cancellation email to customer: ' . $e->getMessage());
            }
        }

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Booking cancelled and notification dispatched.', 'data' => $booking]);
        }

        return redirect()->back()->with('success', 'Booking marked as cancelled and notification email has been dispatched.');
    }

    public function destroyBooking($id)
    {
        $booking = BookingCar::findOrFail($id);
        $booking->delete();

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Booking deleted.']);
        }

        return redirect()->route('admin.bookings')->with('success', 'Booking deleted successfully.');
    }
}

