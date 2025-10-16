<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

/**
 * Trait to automatically track created_by and updated_by
 * Add this trait to models that need tracking
 */
trait HasCreatedUpdatedBy
{
    protected static function bootHasCreatedUpdatedBy()
    {
        // Set created_by when creating
        static::creating(function ($model) {
            if (Auth::check() && empty($model->created_by)) {
                $model->created_by = Auth::id();
            }
        });

        // Set updated_by when updating
        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        // Also set updated_by when saving (covers both create and update)
        static::saving(function ($model) {
            if (Auth::check() && $model->exists) {
                $model->updated_by = Auth::id();
            }
        });
    }

    /**
     * Get the user who created this record
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Get the user who last updated this record
     */
    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }
}



