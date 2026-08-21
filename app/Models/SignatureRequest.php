<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignatureRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'submitted_at' => 'datetime',
        'signed_at' => 'datetime',
        'emailed_at' => 'datetime',
    ];

    public function student() { return $this->belongsTo(Student::class); }
    public function lecturer() { return $this->belongsTo(Lecturer::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewer_id'); }
    public function approver() { return $this->belongsTo(User::class, 'approver_id'); }
    public function histories() { return $this->hasMany(SignatureRequestHistory::class); }
}
