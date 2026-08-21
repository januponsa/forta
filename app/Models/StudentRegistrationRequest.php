<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentRegistrationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'google_id', 'google_email', 'normalized_email', 'nim', 'name', 
        'angkatan', 'google_avatar', 'status', 'conflict_type', 'student_id', 
        'request_note', 'review_note', 'requested_at', 'reviewed_at', 'reviewed_by'
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
