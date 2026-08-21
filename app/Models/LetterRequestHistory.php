<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterRequestHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'internship_letter_request_id',
        'actor_type',
        'actor_id',
        'action',
        'previous_status',
        'new_status',
        'note',
    ];

    public function request()
    {
        return $this->belongsTo(InternshipLetterRequest::class, 'internship_letter_request_id');
    }

    // Dynamic relationship for actor
    public function actor()
    {
        if ($this->actor_type === 'student') {
            return $this->belongsTo(Student::class, 'actor_id');
        } else {
            return $this->belongsTo(User::class, 'actor_id');
        }
    }
}
