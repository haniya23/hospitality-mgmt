<?php

namespace App\Observers;

use App\Models\B2bPartner;
use App\Models\Guest;

class B2bPartnerObserver
{
    /**
     * Handle the B2bPartner "created" event.
     */
    public function created(B2bPartner $b2bPartner): void
    {
        // Automatically create reserved customer for new B2B partner
        $b2bPartner->getOrCreateReservedCustomer();
    }

    /**
     * Handle the B2bPartner "updated" event.
     */
    public function updated(B2bPartner $b2bPartner): void
    {
        // Check if partner name was changed
        if ($b2bPartner->isDirty('partner_name')) {
            $b2bPartner->updateReservedCustomerName();
        }
    }

    /**
     * Handle the B2bPartner "deleted" event.
     */
    public function deleted(B2bPartner $b2bPartner): void
    {
        // Delete the reserved customer when partner is deleted
        if ($b2bPartner->reservedCustomer) {
            $b2bPartner->reservedCustomer->delete();
        }
    }

    /**
     * Handle the B2bPartner "restored" event.
     */
    public function restored(B2bPartner $b2bPartner): void
    {
        // Recreate reserved customer when partner is restored
        $b2bPartner->getOrCreateReservedCustomer();
    }

    /**
     * Handle the B2bPartner "force deleted" event.
     */
    public function forceDeleted(B2bPartner $b2bPartner): void
    {
        // Force delete the reserved customer when partner is force deleted
        if ($b2bPartner->reservedCustomer) {
            $b2bPartner->reservedCustomer->forceDelete();
        }
    }
}