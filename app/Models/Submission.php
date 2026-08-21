<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_REVISION = 'revision';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'form_id',
        'nim',
        'name',
        'email',
        'answers',
        'status',
        'review_note',
        'reviewed_by',
        'reviewed_at',
        'submitted_at',
        'field_review_statuses',
    ];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'field_review_statuses' => 'array',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function files()
    {
        return $this->hasMany(SubmissionFile::class);
    }

    public function reviewerAssignments()
    {
        return $this->hasMany(ReviewerAssignment::class);
    }

    public function assignedReviewers()
    {
        return $this->belongsToMany(User::class, 'reviewer_assignments', 'submission_id', 'user_id')->withPivot('status')->withTimestamps();
    }
}
