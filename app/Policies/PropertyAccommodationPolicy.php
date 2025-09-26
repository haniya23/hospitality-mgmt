<?php

namespace App\Policies;

use App\Models\PropertyAccommodation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyAccommodationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PropertyAccommodation $propertyAccommodation)
    {
        return $user->id === $propertyAccommodation->property->owner_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PropertyAccommodation $propertyAccommodation)
    {
        return $user->id === $propertyAccommodation->property->owner_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PropertyAccommodation $propertyAccommodation)
    {
        return $user->id === $propertyAccommodation->property->owner_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PropertyAccommodation $propertyAccommodation)
    {
        return $user->id === $propertyAccommodation->property->owner_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PropertyAccommodation $propertyAccommodation)
    {
        return $user->id === $propertyAccommodation->property->owner_id;
    }
}