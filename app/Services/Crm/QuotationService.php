<?php

namespace App\Services\Crm;

use App\Models\Crm\Quotation;
use App\Models\Crm\QuotationItem;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class QuotationService
{
    public function getQuotations(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Quotation::with(['customer', 'lead', 'car', 'creator'])
            ->latest('id');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('lead', function ($lq) use ($search) {
                        $lq->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function createQuotation(array $data, array $items = []): Quotation
    {
        return DB::transaction(function () use ($data, $items) {
            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);
            $daysCount = max(1, $startDate->diffInDays($endDate));

            $dailyRate = (float) ($data['daily_rate'] ?? 0);
            $subtotal = $dailyRate * $daysCount;

            // Add item prices if any
            foreach ($items as $item) {
                $qty = (int) ($item['quantity'] ?? 1);
                $unit = (float) ($item['unit_price'] ?? 0);
                $subtotal += ($qty * $unit);
            }

            $discountAmount = (float) ($data['discount_amount'] ?? 0);
            $taxRate = (float) ($data['tax_rate'] ?? 13.00); // 13% default VAT
            $taxable = max(0, $subtotal - $discountAmount);
            $taxAmount = round(($taxable * $taxRate) / 100, 2);
            $totalAmount = $taxable + $taxAmount;

            $quotation = Quotation::create([
                'quotation_number' => 'QUO-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
                'customer_id' => $data['customer_id'] ?? null,
                'lead_id' => $data['lead_id'] ?? null,
                'car_id' => $data['car_id'] ?? null,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'days_count' => $daysCount,
                'daily_rate' => $dailyRate,
                'subtotal' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'status' => $data['status'] ?? 'draft',
                'valid_until' => $data['valid_until'] ?? now()->addDays(14)->toDateString(),
                'terms_conditions' => $data['terms_conditions'] ?? "1. Full fuel to full fuel policy.\n2. Valid driving license and security deposit required upon vehicle handover.\n3. Daily rate includes standard insurance.",
                'notes' => $data['notes'] ?? null,
                'created_by' => auth('admin')->id(),
            ]);

            foreach ($items as $item) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'] ?? 1,
                    'unit_price' => $item['unit_price'] ?? 0,
                    'total_price' => ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0),
                ]);
            }

            return $quotation->load(['items', 'car', 'customer', 'lead']);
        });
    }

    public function updateStatus(Quotation $quotation, string $status): Quotation
    {
        $quotation->update(['status' => $status]);

        return $quotation;
    }

    public function deleteQuotation(Quotation $quotation): bool
    {
        return $quotation->delete();
    }
}
