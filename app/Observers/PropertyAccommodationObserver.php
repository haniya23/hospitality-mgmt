<?php

namespace App\Observers;

use App\Models\PropertyAccommodation;
use App\Models\Guest;

class PropertyAccommodationObserver
{
    /**
     * Handle the PropertyAccommodation "created" event.
     */
    public function created(PropertyAccommodation $propertyAccommodation): void
    {
        // Automatically create reserved customer for new accommodation
        $propertyAccommodation->getOrCreateReservedCustomer();
    }

    /**
     * Handle the PropertyAccommodation "updated" event.
     */
    public function updated(PropertyAccommodation $propertyAccommodation): void
    {
        // Check if accommodation name was changed (custom_name or predefined type)
        if ($propertyAccommodation->isDirty('custom_name') || $propertyAccommodation->isDirty('predefined_accommodation_type_id')) {
            $propertyAccommodation->updateReservedCustomerName();
        }
    }

    /**
     * Handle the PropertyAccommodation "deleted" event.
     */
    public function deleted(PropertyAccommodation $propertyAccommodation): void
    {
        // Delete the reserved customer when accommodation is deleted
        if ($propertyAccommodation->reservedCustomer) {
            $propertyAccommodation->reservedCustomer->delete();
        }
    }

    /**
     * Handle the PropertyAccommodation "restored" event.
     */
    public function restored(PropertyAccommodation $propertyAccommodation): void
    {
        // Recreate reserved customer when accommodation is restored
        $propertyAccommodation->getOrCreateReservedCustomer();
    }

    /**
     * Handle the PropertyAccommodation "force deleted" event.
     */
    public function forceDeleted(PropertyAccommodation $propertyAccommodation): void
    {
        // Force delete the reserved customer when accommodation is force deleted
        if ($propertyAccommodation->reservedCustomer) {
            $propertyAccommodation->reservedCustomer->forceDelete();
        }
    }
}
