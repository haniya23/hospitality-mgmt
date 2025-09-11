<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class B2bRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_partner_id',
        'to_property_id',
        'guest_id',
        'check_in_date',
        'check_out_date',
        'adults',
        'children',
        'quoted_price',
        'counter_price',
        'status',
        'initial_notes',
        'negotiation_history',
        'converted_booking_id',
        'expires_at',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'quoted_price' => 'decimal:2',
        'counter_price' => 'decimal:2',
        'negotiation_history' => 'array',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
            // Set expiry to 48 hours by default
            if (empty($model->expires_at)) {
                $model->expires_at = now()->addHours(48);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function fromPartner()
    {
        return $this->belongsTo(User::class, 'from_partner_id');
    }

    public function toProperty()
    {
        return $this->belongsTo(Property::class, 'to_property_id');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function convertedBooking()
    {
        return $this->belongsTo(Reservation::class, 'converted_booking_id');
    }

    // Negotiation methods
    public function addNegotiationMessage($message, $price = null, $userId = null)
    {
        $history = $this->negotiation_history ?? [];
        $history[] = [
            'message' => $message,
            'price' => $price,
            'user_id' => $userId ?? auth()->id(),
            'timestamp' => now()->toISOString(),
        ];
        
        $this->update(['negotiation_history' => $history]);
    }

    public function counter($newPrice, $message = null)
    {
        $this->update([
            'counter_price' => $newPrice,
            'status' => 'countered',
            'expires_at' => now()->addHours(48), // Reset expiry
        ]);

        $this->addNegotiationMessage($message ?? "Counter offer: ₹{$newPrice}", $newPrice);
    }

    public function accept()
    {
        $this->update(['status' => 'accepted']);
        
        // Convert to booking
        $booking = Reservation::create([
            'guest_id' => $this->guest_id,
            'property_accommodation_id' => $this->getAvailableAccommodation(),
            'b2b_partner_id' => $this->getB2bPartnerId(),
            'check_in_date' => $this->check_in_date,
            'check_out_date' => $this->check_out_date,
            'adults' => $this->adults,
            'children' => $this->children,
            'total_amount' => $this->counter_price ?? $this->quoted_price,
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        $this->update(['converted_booking_id' => $booking->id]);
        
        return $booking;
    }

    public function reject($reason = null)
    {
        $this->update(['status' => 'rejected']);
        if ($reason) {
            $this->addNegotiationMessage("Request rejected: {$reason}");
        }
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    private function getAvailableAccommodation()
    {
        // Find available accommodation for the property
        return $this->toProperty->propertyAccommodations()->first()?->id;
    }

    private function getB2bPartnerId()
    {
        // Get B2B partner record for the requesting user
        return B2bPartner::where('contact_user_id', $this->from_partner_id)->first()?->id;
    }
}