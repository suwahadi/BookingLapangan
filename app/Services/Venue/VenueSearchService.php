<?php

namespace App\Services\Venue;

use App\Models\Venue;
use App\Repositories\Booking\AvailabilityQueryRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VenueSearchService
{
    public function __construct(
        private readonly AvailabilityQueryRepository $availabilityQueryRepository
    ) {}

    /**
     * Cari venue yang memiliki minimal 1 court tersedia pada rentang waktu.
     */
    public function search(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $sport = trim((string)($filters['sport_type'] ?? ''));
        $keyword = trim((string)($filters['keyword'] ?? ''));
        $date = (string)($filters['date'] ?? '');
        $start = (string)($filters['start_time'] ?? '');
        $end = (string)($filters['end_time'] ?? '');

        $venues = Venue::query()
            ->where('is_active', true)
            ->when($keyword !== '', function ($q) use ($keyword) {
                $q->where(function ($q2) use ($keyword) {
                    $q2->where('name', 'like', '%' . $keyword . '%')
                       ->orWhere('city', 'like', '%' . $keyword . '%')
                       ->orWhere('address', 'like', '%' . $keyword . '%');
                });
            })
            ->whereHas('courts', function ($courts) use ($sport, $date, $start, $end) {
                $courts->where('is_active', true);

                if ($sport !== '') {
                    $courts->where('sport', $sport); // Updated to 'sport' column
                }

                if ($date && $start && $end) {
                    $this->availabilityQueryRepository
                        ->scopeAvailableForRange($courts, $date, $start, $end);
                }
            })
            ->with(['media' => function ($q) {
                $q->orderBy('order_column');
            }])
            ->withCount(['courts as active_courts_count' => fn ($q) => $q->where('is_active', true)])
            ->withMin('pricings', 'price_per_hour')
            ->orderBy('name');

        return $venues->paginate($perPage);
    }
}
