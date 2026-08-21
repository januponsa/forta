<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailBlastRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_blast_id',
        'student_id',
        'name',
        'nim',
        'email',
        'angkatan',
        'status',
        'error_message',
        'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function emailBlast()
    {
        return $this->belongsTo(EmailBlast::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
