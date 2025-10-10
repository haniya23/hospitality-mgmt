<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any tasks.
     */
    public function viewAny(User $user): bool
    {
        // Everyone with staff access can view tasks
        return $user->isOwner() || $user->staffMember !== null;
    }

    /**
     * Determine whether the user can view the task.
     */
    public function view(User $user, Task $task): bool
    {
        // Owner can view all tasks in their properties
        if ($user->isOwner()) {
            return $task->property->owner_id === $user->id;
        }

        // Staff member can view tasks in their property
        if ($user->staffMember) {
            return $user->staffMember->property_id === $task->property_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create tasks.
     */
    public function create(User $user): bool
    {
        // Owners, managers, and supervisors can create tasks
        return $user->isOwner() 
            || $user->staffMember?->isManager() 
            || $user->staffMember?->isSupervisor();
    }

    /**
     * Determine whether the user can update the task.
     */
    public function update(User $user, Task $task): bool
    {
        // Owner can update tasks in their properties
        if ($user->isOwner()) {
            return $task->property->owner_id === $user->id;
        }

        // Task creator can update
        if ($task->created_by === $user->id) {
            return true;
        }

        // Manager can update tasks in their property
        if ($user->staffMember?->isManager()) {
            return $user->staffMember->property_id === $task->property_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the task.
     */
    public function delete(User $user, Task $task): bool
    {
        // Only owners and task creators (if manager) can delete
        if ($user->isOwner()) {
            return $task->property->owner_id === $user->id;
        }

        if ($user->staffMember?->isManager() && $task->created_by === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can assign the task.
     */
    public function assign(User $user, Task $task): bool
    {
        // Owner can assign tasks in their properties
        if ($user->isOwner()) {
            return $task->property->owner_id === $user->id;
        }

        // Manager can assign tasks
        if ($user->staffMember?->isManager()) {
            return $user->staffMember->property_id === $task->property_id;
        }

        // Supervisor can assign tasks
        if ($user->staffMember?->isSupervisor()) {
            return $user->staffMember->property_id === $task->property_id;
        }

        return false;
    }

    /**
     * Determine whether the user can start the task (execute it).
     */
    public function start(User $user, Task $task): bool
    {
        // Only the assigned staff member can start the task
        if ($user->staffMember && $task->assigned_to === $user->staffMember->id) {
            return $task->canBeStarted();
        }

        return false;
    }

    /**
     * Determine whether the user can complete the task.
     */
    public function complete(User $user, Task $task): bool
    {
        // Only the assigned staff member can complete the task
        if ($user->staffMember && $task->assigned_to === $user->staffMember->id) {
            return $task->canBeCompleted();
        }

        return false;
    }

    /**
     * Determine whether the user can verify the task.
     */
    public function verify(User $user, Task $task): bool
    {
        // Owner can verify
        if ($user->isOwner()) {
            return $task->property->owner_id === $user->id && $task->canBeVerified();
        }

        // Manager can verify
        if ($user->staffMember?->isManager()) {
            return $user->staffMember->property_id === $task->property_id && $task->canBeVerified();
        }

        // Supervisor can verify their subordinates' tasks
        if ($user->staffMember?->isSupervisor() && $task->assignedStaff) {
            return $task->assignedStaff->reports_to === $user->staffMember->id && $task->canBeVerified();
        }

        return false;
    }

    /**
     * Determine whether the user can reject the task.
     */
    public function reject(User $user, Task $task): bool
    {
        // Same as verify - only supervisors and above can reject
        return $this->verify($user, $task);
    }

    /**
     * Determine whether the user can upload media to the task.
     */
    public function uploadMedia(User $user, Task $task): bool
    {
        // Assigned staff can upload proof
        if ($user->staffMember && $task->assigned_to === $user->staffMember->id) {
            return true;
        }

        // Supervisors and managers can also upload
        if ($user->staffMember?->canVerifyTasks()) {
            return $user->staffMember->property_id === $task->property_id;
        }

        return false;
    }
}
