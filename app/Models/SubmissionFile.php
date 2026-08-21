<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionFile extends Model
{
    protected $fillable = [
        'submission_id',
        'field_id',
        'original_name',
        'stored_path',
        'mime_type',
        'size_bytes',
        'uploaded_at',
        'review_status',
        'review_note',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
            'size_bytes' => 'integer',
        ];
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function field()
    {
        return $this->belongsTo(FormField::class, 'field_id');
    }
}
