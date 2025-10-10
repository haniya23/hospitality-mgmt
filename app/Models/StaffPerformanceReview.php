<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StaffPerformanceReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_member_id',
        'reviewed_by',
        'review_period_start',
        'review_period_end',
        'task_completion_rate',
        'average_task_rating',
        'punctuality_score',
        'strengths',
        'areas_for_improvement',
        'goals',
        'overall_rating',
    ];

    protected $casts = [
        'review_period_start' => 'date',
        'review_period_end' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Relationships
    public function staffMember()
    {
        return $this->belongsTo(StaffMember::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Helper methods
    public function getRatingBadgeColor()
    {
        return match($this->overall_rating) {
            'outstanding' => 'bg-purple-100 text-purple-800',
            'exceeds_expectations' => 'bg-green-100 text-green-800',
            'meets_expectations' => 'bg-blue-100 text-blue-800',
            'needs_improvement' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getRatingLabel()
    {
        return match($this->overall_rating) {
            'outstanding' => 'Outstanding',
            'exceeds_expectations' => 'Exceeds Expectations',
            'meets_expectations' => 'Meets Expectations',
            'needs_improvement' => 'Needs Improvement',
            default => 'Not Rated'
        };
    }
}
