<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternshipLetterRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'company_name',
        'recipient_name',
        'company_address',
        'company_city',
        'placement_location',
        'internship_position',
        'start_date',
        'end_date',
        'duration_notes',
        'purpose',
        'additional_notes',
        'attachment_path',
        'final_pdf_path',
        'status',
        'letter_number',
        'generated_at',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'revision_note',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'generated_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function histories()
    {
        return $this->hasMany(LetterRequestHistory::class);
    }
}
