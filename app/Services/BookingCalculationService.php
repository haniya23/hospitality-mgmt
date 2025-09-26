<?php

namespace App\Services;

class BookingCalculationService
{
    /**
     * Calculate total guests
     */
    public function calculateTotalGuests(int $adults, int $children): int
    {
        return $adults + $children;
    }

    /**
     * Calculate days and nights
     */
    public function calculateDaysNights(string $checkInDate, string $checkOutDate): array
    {
        if (!$checkInDate || !$checkOutDate) {
            return ['days' => 0, 'nights' => 0];
        }

        $checkIn = new \DateTime($checkInDate);
        $checkOut = new \DateTime($checkOutDate);
        $diffTime = $checkOut->diff($checkIn);
        
        $days = $diffTime->days;
        $nights = $days > 0 ? $days - 1 : 0;

        return [
            'days' => $days,
            'nights' => $nights
        ];
    }

    /**
     * Calculate default amount based on booking type
     */
    public function calculateDefaultAmount(
        string $bookingType,
        float $basePrice,
        int $nights,
        int $totalGuests,
        float $perPersonPrice = 1000.0
    ): float {
        if ($bookingType === 'per_person') {
            return $perPersonPrice * $totalGuests * $nights;
        }
        
        return $basePrice * $nights;
    }

    /**
     * Calculate balance pending
     */
    public function calculateBalancePending(float $totalAmount, float $advancePaid): float
    {
        return $totalAmount - $advancePaid;
    }

    /**
     * Calculate commission amount
     */
    public function calculateCommission(
        string $commissionType,
        float $commissionValue,
        float $totalAmount
    ): array {
        if ($commissionType === 'percentage') {
            $commissionAmount = ($totalAmount * $commissionValue) / 100;
            $commissionPercentage = $commissionValue;
        } else {
            $commissionAmount = $commissionValue;
            $commissionPercentage = $totalAmount > 0 ? ($commissionValue / $totalAmount) * 100 : 0;
        }

        $netAmount = $totalAmount - $commissionAmount;

        return [
            'commission_amount' => $commissionAmount,
            'commission_percentage' => $commissionPercentage,
            'net_amount' => $netAmount
        ];
    }

    /**
     * Get calculation summary
     */
    public function getCalculationSummary(array $data): array
    {
        $totalGuests = $this->calculateTotalGuests($data['adults'], $data['children']);
        $daysNights = $this->calculateDaysNights($data['check_in_date'], $data['check_out_date']);
        
        $defaultAmount = $this->calculateDefaultAmount(
            $data['booking_type'],
            $data['base_price'],
            $daysNights['nights'],
            $totalGuests,
            $data['per_person_price'] ?? 1000.0
        );

        $totalAmount = $data['total_amount'] ?? $defaultAmount;
        $balancePending = $this->calculateBalancePending($totalAmount, $data['advance_paid'] ?? 0);

        $commission = [];
        if ($data['is_b2b'] && ($data['commission_value'] ?? 0) > 0) {
            $commission = $this->calculateCommission(
                $data['commission_type'],
                $data['commission_value'],
                $totalAmount
            );
        }

        return [
            'total_guests' => $totalGuests,
            'days' => $daysNights['days'],
            'nights' => $daysNights['nights'],
            'default_amount' => $defaultAmount,
            'total_amount' => $totalAmount,
            'balance_pending' => $balancePending,
            'commission' => $commission
        ];
    }
}

