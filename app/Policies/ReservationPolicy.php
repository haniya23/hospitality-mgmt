<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the reservation.
     */
    public function view(User $user, Reservation $reservation)
    {
        return $reservation->accommodation->property->owner_id === $user->id;
    }

    /**
     * Determine whether the user can update the reservation.
     */
    public function update(User $user, Reservation $reservation)
    {
        return $reservation->accommodation->property->owner_id === $user->id;
    }

    /**
     * Determine whether the user can delete the reservation.
     */
    public function delete(User $user, Reservation $reservation)
    {
        return $reservation->accommodation->property->owner_id === $user->id;
    }
}
