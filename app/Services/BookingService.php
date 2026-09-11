<?php

namespace App\Services;

use App\DTOs\BookingDTO;
use App\Mail\Admin\BookingStatusNotificationMail;
use App\Models\BookingCar;
use App\Repositories\BookingRepositoryInterface;
use App\Repositories\CarRepositoryInterface;
use App\Services\Admin\AdminCountCacheService;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingService
{
    public function __construct(
        private BookingRepositoryInterface $bookingRepository,
        private CarRepositoryInterface $carRepository,
    ) {}

    public function getBookedAndReservedDates(int $carId): array
    {
        $bookings = $this->bookingRepository->getBookingsForCar($carId);
        $bookedDates = [];
        $reservedDates = [];

        foreach ($bookings as $booking) {
            $start = Carbon::parse($booking->pick_up_date);
            $end = Carbon::parse($booking->last_date);

            while ($start <= $end) {
                if ($booking->status === 'booked') {
                    $bookedDates[] = $start->format('Y-m-d');
                } elseif ($booking->status === 'reserved') {
                    $reservedDates[] = $start->format('Y-m-d');
                }
                $start->addDay();
            }
        }

        return [
            'booked' => array_unique($bookedDates),
            'reserved' => array_unique($reservedDates),
        ];
    }

    public function hasDateConflict(int $carId, \DateTimeInterface|string|null $startDate, \DateTimeInterface|string|null $endDate): bool
    {
        if (! $startDate || ! $endDate) {
            return false;
        }

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $existingBookings = $this->bookingRepository->getBookingsForCar($carId);

        foreach ($existingBookings as $booking) {
            if ($this->datesOverlap(
                $start,
                $end,
                Carbon::parse($booking->pick_up_date),
                Carbon::parse($booking->last_date)
            )) {
                return true;
            }
        }

        return false;
    }

    public function datesOverlap(Carbon $start1, Carbon $end1, Carbon $start2, Carbon $end2): bool
    {
        return $start1 <= $end2 && $end1 >= $start2;
    }

    public function calculateTotalPrice(int $carId, \DateTimeInterface|string|null $startDate, \DateTimeInterface|string|null $endDate, float $distanceTraveled = 0): float
    {
        $car = $this->carRepository->findOrFail($carId);

        if (! $startDate || ! $endDate) {
            return (float) $car->car_price_per_day;
        }

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $days = $start->diffInDays($end) + 1;

        return $days > 0
            ? $days * $car->car_price_per_day
            : ($distanceTraveled > 0 ? $distanceTraveled * $car->car_price_per_km : 0);
    }

    public function createReservation(BookingDTO $dto): BookingCar
    {
        $booking = $this->bookingRepository->create([
            'pickup_location' => $dto->pickup_location,
            'drop_location' => $dto->drop_location,
            'pick_up_date' => $dto->pick_up_date?->format('Y-m-d'),
            'last_date' => $dto->last_date?->format('Y-m-d'),
            'total_price' => $dto->total_price,
            'status' => 'reserved',
            'car_id' => $dto->car_id,
            'customer_id' => $dto->customer_id,
            'purpose' => $dto->purpose,
            'other_purpose' => $dto->other_purpose,
        ]);

        AdminCountCacheService::clear();

        return $booking;
    }

    public function reserveCar(BookingDTO $dto, float $distanceTraveled = 0): ?BookingCar
    {
        if ($this->hasDateConflict($dto->car_id, $dto->pick_up_date, $dto->last_date)) {
            return null;
        }

        $totalPrice = $this->calculateTotalPrice($dto->car_id, $dto->pick_up_date, $dto->last_date, $distanceTraveled);

        $booking = $this->bookingRepository->create([
            'pickup_location' => $dto->pickup_location,
            'drop_location' => $dto->drop_location,
            'pick_up_date' => $dto->pick_up_date?->format('Y-m-d'),
            'last_date' => $dto->last_date?->format('Y-m-d'),
            'total_price' => $totalPrice,
            'status' => 'reserved',
            'car_id' => $dto->car_id,
            'customer_id' => $dto->customer_id,
            'purpose' => $dto->purpose,
            'other_purpose' => $dto->other_purpose,
        ]);

        AdminCountCacheService::clear();

        return $booking;
    }

    public function getOwnerBookings(int $ownerId): Collection
    {
        return $this->bookingRepository->getByOwner($ownerId);
    }

    public function confirmBooking(int $bookingId, int $ownerId): BookingCar
    {
        $booking = $this->bookingRepository->findOrFail($bookingId);

        if ($booking->car->owner_id !== $ownerId) {
            abort(403, 'Unauthorized action.');
        }

        $booking->status = 'booked';
        $booking->save();

        AdminCountCacheService::clear();

        return $booking;
    }

    public function cancelBookingByOwner(int $bookingId, int $ownerId): BookingCar
    {
        $booking = $this->bookingRepository->findOrFail($bookingId);

        if ($booking->car->owner_id !== $ownerId) {
            abort(403, 'Unauthorized action.');
        }

        $booking->status = 'canceled';
        $booking->save();

        AdminCountCacheService::clear();

        return $booking;
    }

    public function getCustomerBookings(int $customerId): Collection
    {
        return $this->bookingRepository->getCustomerBookings($customerId);
    }

    public function getBookingById(int $id): ?BookingCar
    {
        return $this->bookingRepository->find($id);
    }

    public function getBookingWithCar(int $id): ?BookingCar
    {
        return $this->bookingRepository->find($id);
    }

    public function cancelBookingByCustomer(int $bookingId, int $customerId): BookingCar
    {
        $booking = $this->bookingRepository->findOrFail($bookingId);

        if ($booking->customer_id !== $customerId) {
            abort(403, 'You can only cancel your own bookings.');
        }

        if ($booking->status === 'cancel') {
            throw new \RuntimeException('This booking cannot be canceled.');
        }

        $booking->status = 'cancel';
        $booking->save();

        AdminCountCacheService::clear();

        return $booking;
    }

    public function processPayment(int $bookingId): BookingCar
    {
        $booking = $this->bookingRepository->findOrFail($bookingId);

        $booking->status = 'booked';
        $booking->save();

        AdminCountCacheService::clear();

        return $booking;
    }

    public function getDisabledDatesForCar(int $carId): array
    {
        $bookings = $this->bookingRepository->getDisabledBookings($carId);
        $disabledDates = [];

        foreach ($bookings as $b) {
            $curr = Carbon::parse($b->pick_up_date);
            $end = Carbon::parse($b->last_date);
            while ($curr <= $end) {
                $disabledDates[] = $curr->format('Y-m-d');
                $curr->addDay();
            }
        }

        return array_values(array_unique($disabledDates));
    }

    public function getCalendarBookings(?int $carId = null)
    {
        return $this->bookingRepository->getCalendarBookings($carId);
    }

    public function getActiveBookingDates(int $carId): array
    {
        $bookings = $this->bookingRepository->getActiveBookingsByCar($carId);

        $dates = [
            'booked' => [],
            'reserved' => [],
        ];

        foreach ($bookings as $booking) {
            $currentDate = $booking->pick_up_date;
            while (strtotime($currentDate) <= strtotime($booking->last_date)) {
                if (in_array($booking->status, ['booked', 'confirm', 'confirmed'])) {
                    $dates['booked'][] = $currentDate;
                } else {
                    $dates['reserved'][] = $currentDate;
                }
                $currentDate = date('Y-m-d', strtotime($currentDate.' +1 day'));
            }
        }

        return $dates;
    }

    public function getTotalBookingsCount(): int
    {
        return $this->bookingRepository->count();
    }

    public function getAdminBookings(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->bookingRepository->getAdminPaginatedBookings($filters, $perPage);
    }

    public function getBookingStatusCounts(): array
    {
        return $this->bookingRepository->getBookingStatusCounts();
    }

    public function confirmAdminBookingAndNotify(int $id): BookingCar
    {
        $booking = $this->bookingRepository->confirmBooking($id);

        $recipientEmail = $booking->customer->email ?? $booking->email ?? null;
        if (filled($recipientEmail)) {
            try {
                Mail::to($recipientEmail)->send(new BookingStatusNotificationMail($booking, 'confirm'));
            } catch (\Throwable $e) {
                Log::warning('Failed to send booking confirmation email to customer: '.$e->getMessage());
            }
        }

        AdminCountCacheService::clear();

        return $booking;
    }

    public function cancelAdminBookingAndNotify(int $id): BookingCar
    {
        $booking = $this->bookingRepository->cancelBooking($id);

        $recipientEmail = $booking->customer->email ?? $booking->email ?? null;
        if (filled($recipientEmail)) {
            try {
                Mail::to($recipientEmail)->send(new BookingStatusNotificationMail($booking, 'cancel'));
            } catch (\Throwable $e) {
                Log::warning('Failed to send booking cancellation email to customer: '.$e->getMessage());
            }
        }

        AdminCountCacheService::clear();

        return $booking;
    }

    public function deleteBooking(int $id): bool
    {
        $result = $this->bookingRepository->deleteBooking($id);
        AdminCountCacheService::clear();

        return $result;
    }

    public function createCustomerBooking(int $customerId, array $data): BookingCar
    {
        $data['customer_id'] = $customerId;
        $booking = $this->bookingRepository->createBooking($data);
        AdminCountCacheService::clear();

        return $booking;
    }

    public function updateCustomerBooking(int $customerId, int $bookingId, array $data): BookingCar
    {
        $booking = $this->bookingRepository->updateBooking($bookingId, $data);
        AdminCountCacheService::clear();

        return $booking;
    }

    public function deleteCustomerBooking(int $customerId, int $bookingId): bool
    {
        $result = $this->bookingRepository->deleteBooking($bookingId);
        AdminCountCacheService::clear();

        return $result;
    }

    public function getRecentBookings(int $perPage = 8): LengthAwarePaginator
    {
        return $this->bookingRepository->getRecentBookings($perPage);
    }

    public function getBookingTrends(): array
    {
        return $this->bookingRepository->getBookingTrends();
    }

    public function getTotalRevenue(): float
    {
        return $this->bookingRepository->getTotalRevenue();
    }

    public function getConfirmedBookingsCount(): int
    {
        return $this->bookingRepository->getConfirmedBookingsCount();
    }

    public function getPendingBookingsCount(): int
    {
        return $this->bookingRepository->getPendingBookingsCount();
    }

    public function getBookingsByCarIds(array $carIds): Collection
    {
        return $this->bookingRepository->getBookingsByCarIds($carIds);
    }

    public function getOwnerPaginatedBookings(int $ownerId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $carIds = \App\Models\Car::where('owner_id', $ownerId)->pluck('id')->toArray();
        $status = $filters['status'] ?? 'all';
        $search = $filters['search'] ?? null;

        $query = BookingCar::with([
            'car:id,car_name,car_model,car_number,car_photo,car_price_per_day,owner_id',
            'customer:id,first_name,last_name,name,email,mobile,phone_number,address',
            'payment',
        ])->whereIn('car_id', $carIds);

        if (! empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('pickup_location', 'like', "%{$search}%")
                    ->orWhere('drop_location', 'like', "%{$search}%")
                    ->orWhereHas('car', function ($cq) use ($search) {
                        $cq->where('car_name', 'like', "%{$search}%")
                            ->orWhere('car_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function getOwnerBookingStatusCounts(int $ownerId): array
    {
        $carIds = \App\Models\Car::where('owner_id', $ownerId)->pluck('id')->toArray();

        return [
            'all' => empty($carIds) ? 0 : BookingCar::whereIn('car_id', $carIds)->count(),
            'pending' => empty($carIds) ? 0 : BookingCar::whereIn('car_id', $carIds)->where('status', 'pending')->count(),
            'confirmed' => empty($carIds) ? 0 : BookingCar::whereIn('car_id', $carIds)->where('status', 'confirmed')->count(),
            'completed' => empty($carIds) ? 0 : BookingCar::whereIn('car_id', $carIds)->where('status', 'completed')->count(),
            'cancelled' => empty($carIds) ? 0 : BookingCar::whereIn('car_id', $carIds)->where('status', 'cancelled')->count(),
        ];
    }

    public function confirmOwnerBooking(int $ownerId, int $bookingId): BookingCar
    {
        $carIds = \App\Models\Car::where('owner_id', $ownerId)->pluck('id')->toArray();
        $booking = BookingCar::whereIn('car_id', $carIds)->findOrFail($bookingId);
        $booking->status = 'confirmed';
        $booking->save();
        AdminCountCacheService::clear();

        return $booking;
    }

    public function cancelOwnerBooking(int $ownerId, int $bookingId): BookingCar
    {
        $carIds = \App\Models\Car::where('owner_id', $ownerId)->pluck('id')->toArray();
        $booking = BookingCar::whereIn('car_id', $carIds)->findOrFail($bookingId);
        $booking->status = 'cancelled';
        $booking->save();
        AdminCountCacheService::clear();

        return $booking;
    }

    public function getOwnerCalendarData(int $ownerId, ?int $carId = null): array
    {
        $cars = \App\Models\Car::where('owner_id', $ownerId)
            ->select(['id', 'car_name', 'car_model', 'car_number', 'car_price_per_day', 'car_photo', 'status', 'available'])
            ->get();

        $carIds = $cars->pluck('id')->toArray();
        $selectedCarId = null;
        if ($carId && in_array((int) $carId, $carIds)) {
            $selectedCarId = (int) $carId;
        }

        $bookingsQuery = BookingCar::with([
            'car:id,car_name,car_model,car_number,car_photo,car_price_per_day,owner_id',
            'customer:id,first_name,last_name,name,email,mobile,phone_number',
        ])
            ->whereIn('car_id', $carIds)
            ->whereIn('status', ['confirmed', 'pending', 'completed']);

        if ($selectedCarId) {
            $bookingsQuery->where('car_id', $selectedCarId);
        }

        $bookings = $bookingsQuery->get();

        $thisMonthBookings = empty($carIds) ? 0 : BookingCar::whereIn('car_id', $carIds)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $stats = [
            'totalCars' => $cars->count(),
            'thisMonthBookings' => $thisMonthBookings,
            'activeBookings' => $bookings->count(),
        ];

        return [
            'carId' => $selectedCarId,
            'cars' => $cars,
            'bookings' => $bookings,
            'stats' => $stats,
        ];
    }

    public function getCustomerCalendarBookings(): Collection
    {
        return BookingCar::with(['car.driver', 'car.owner'])
            ->whereIn('status', ['confirm', 'booked', 'pending'])
            ->orderBy('pick_up_date', 'asc')
            ->get();
    }

    public function getBookedRangesForCar(int $carId): Collection
    {
        return BookingCar::where('car_id', $carId)
            ->whereIn('status', ['confirm', 'booked', 'pending', 'reserved'])
            ->get(['pick_up_date', 'last_date', 'status'])
            ->map(function ($b) {
                return [
                    'start' => substr($b->pick_up_date, 0, 10),
                    'end' => substr($b->last_date, 0, 10),
                    'status' => $b->status,
                ];
            });
    }
}
