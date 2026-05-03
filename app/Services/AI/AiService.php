<?php

namespace App\Services\AI;

use App\Models\AiChat;
use App\Models\BookingCar;
use App\Models\Car;
use Illuminate\Support\Facades\Auth;
use Psr\Http\Message\StreamInterface;

class AiService
{
    private ?string $cachedDatabaseContext = null;

    public function __construct(
        protected AiChat $aiChat,
        protected GroqService $groqService
    ) {}

    public function getHistory(string $sessionId, ?int $userId)
    {
        $query = $this->aiChat->query()->orderBy('id', 'desc');

        if ($userId) {
            $query->where(function ($builder) use ($userId, $sessionId) {
                $builder->where('user_id', $userId)
                    ->orWhere('session_id', $sessionId);
            });
        } else {
            $query->where('session_id', $sessionId);
        }

        return $query->limit(8)->get(['prompt', 'response'])->reverse();
    }

    public function ask(string $prompt, string $sessionId): string
    {
        $userId = Auth::id();
        $history = $this->getHistory($sessionId, $userId)?->toArray() ?? [];
        $databaseContext = $this->getCachedDatabaseContext();

        return $this->groqService->ask($prompt, $history, $databaseContext);
    }

    public function askStream(string $prompt, string $sessionId): StreamInterface
    {
        throw new \Exception('Streaming not needed with Groq - responses are ~100ms');
    }

    private function getCachedDatabaseContext(): string
    {
        if ($this->cachedDatabaseContext === null) {
            $this->cachedDatabaseContext = $this->buildDatabaseContext();
        }

        return $this->cachedDatabaseContext;
    }

    public function storeChat(string $sessionId, string $prompt, string $response): void
    {
        try {
            $userId = Auth::id() ?? null;

            if ($userId) {
                $userName = Auth::user()->name ?? 'User';
            } else {
                $userName = 'Guest';
            }

            $this->aiChat->create([
                'user_id' => $userId,
                'user_name' => $userName,
                'session_id' => $sessionId,
                'prompt' => $prompt,
                'response' => $response,
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function buildDatabaseContext(): string
    {
        $availableCars = Car::query()
            ->where('available', 'yes')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get(['id', 'car_name', 'car_model', 'car_number', 'owner_id']);

        $recentBookings = BookingCar::query()
            ->orderBy('id', 'desc')
            ->limit(3)
            ->get(['id', 'status', 'car_id', 'pick_up_date', 'last_date']);

        $lines = [];
        $lines[] = 'Available Cars:';

        if ($availableCars->isEmpty()) {
            $lines[] = '- None available';
        } else {
            foreach ($availableCars as $car) {
                $lines[] = sprintf(
                    '- %s (%s) [Owner: %s]',
                    $car->car_name ?? 'N/A',
                    $car->car_number ?? 'N/A',
                    $car->owner_id ?? 'N/A'
                );
            }
        }

        $lines[] = 'Recent Bookings:';

        if ($recentBookings->isEmpty()) {
            $lines[] = '- None';
        } else {
            foreach ($recentBookings as $booking) {
                $lines[] = sprintf(
                    '- Booking #%s (Car %s) Status: %s | %s to %s',
                    $booking->id ?? 'N/A',
                    $booking->car_id ?? 'N/A',
                    $booking->status ?? 'N/A',
                    $booking->pick_up_date ?? 'N/A',
                    $booking->last_date ?? 'N/A'
                );
            }
        }

        return implode("\n", $lines);
    }
}
