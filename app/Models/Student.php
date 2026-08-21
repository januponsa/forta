<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'nim',
        'name',
        'email',
        'normalized_email',
        'angkatan',
        'google_id',
        'avatar',
        'academic_status',
        'login_enabled',
        'approval_status',
        'approved_at',
        'approved_by',
        'last_login_at',
        'source_type',
        'source_batch',
        'source_row',
        'source_hash',
        'manual_override',
        'status_akademik',
        'status_akun',
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'login_enabled' => 'boolean',
            'manual_override' => 'boolean',
            'approved_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }
    
    /**
     * Scope a query to only include active and approved students.
     */
    public function scopeActiveAndApproved($query)
    {
        return $query->where('academic_status', 'active')
                     ->where('login_enabled', true)
                     ->where('approval_status', 'approved');
    }
}
