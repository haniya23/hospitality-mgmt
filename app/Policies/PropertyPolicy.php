<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PropertyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Allow users to view properties
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Property $property): bool
    {
        return $user->id === $property->owner_id || $user->is_admin;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Allow authenticated users to create properties
    }

    /**
     * Determine whether the user can create accommodations for this property.
     */
    public function createAccommodation(User $user, Property $property): bool
    {
        return $user->id === $property->owner_id || $user->is_admin;
    }

    /**
     * Determine whether the user can update accommodations for this property.
     */
    public function updateAccommodation(User $user, Property $property): bool
    {
        return $user->id === $property->owner_id || $user->is_admin;
    }

    /**
     * Determine whether the user can delete accommodations for this property.
     */
    public function deleteAccommodation(User $user, Property $property): bool
    {
        return $user->id === $property->owner_id || $user->is_admin;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Property $property): bool
    {
        return $user->id === $property->owner_id || $user->is_admin;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Property $property): bool
    {
        return $user->id === $property->owner_id || $user->is_admin;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Property $property): bool
    {
        return $user->id === $property->owner_id || $user->is_admin;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Property $property): bool
    {
        return $user->is_admin; // Only admins can force delete
    }
}
