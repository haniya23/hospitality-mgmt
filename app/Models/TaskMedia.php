<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class TaskMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'uploaded_by',
        'file_path',
        'file_name',
        'file_type',
        'mime_type',
        'file_size',
        'media_type',
        'caption',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });

        static::deleting(function ($model) {
            // Delete file from storage when model is deleted
            if (Storage::disk('public')->exists($model->file_path)) {
                Storage::disk('public')->delete($model->file_path);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Relationships
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Scopes
    public function scopeProofPhotos($query)
    {
        return $query->where('media_type', 'proof');
    }

    public function scopeBeforePhotos($query)
    {
        return $query->where('media_type', 'before');
    }

    public function scopeAfterPhotos($query)
    {
        return $query->where('media_type', 'after');
    }

    // Helper methods
    public function getUrl()
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public function getFormattedFileSize()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }

    public function isImage()
    {
        return $this->file_type === 'image';
    }

    public function isDocument()
    {
        return $this->file_type === 'document';
    }

    public function isVideo()
    {
        return $this->file_type === 'video';
    }
}
