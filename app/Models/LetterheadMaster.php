<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LetterheadMaster extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'code', 'unit',
        'university_name', 'faculty', 'study_program',
        'campus_address', 'phone', 'website', 'email',
        'status', 'active_version_id', 'effective_date',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
        ];
    }

    public function versions()
    {
        return $this->hasMany(LetterheadVersion::class);
    }

    public function activeVersion()
    {
        return $this->belongsTo(LetterheadVersion::class, 'active_version_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function templateVersions()
    {
        return $this->hasManyThrough(
            DocumentTemplateVersion::class,
            LetterheadVersion::class,
            'letterhead_master_id',
            'letterhead_version_id'
        );
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'published');
    }
}
