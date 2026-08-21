<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'type', 'document_purpose', 'category', 'editor_type',
        'status', 'active_version_id', 'letterhead_version_id',
        'effective_date',
        'header_html', 'body_html', 'footer_html',
        'paper_size', 'margin_top', 'margin_bottom', 'margin_left', 'margin_right',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
        ];
    }

    // --- Relationships ---

    public function versions()
    {
        return $this->hasMany(DocumentTemplateVersion::class);
    }

    public function activeVersion()
    {
        return $this->belongsTo(DocumentTemplateVersion::class, 'active_version_id');
    }

    public function letterheadVersion()
    {
        return $this->belongsTo(LetterheadVersion::class);
    }

    public function instances()
    {
        return $this->hasMany(DocumentInstance::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // --- Scopes ---

    public function scopeActive($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeForPurpose($query, string $purpose)
    {
        return $query->where('document_purpose', $purpose);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // --- Helpers ---

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Get the latest version number for this template.
     */
    public function getLatestVersionNumber(): int
    {
        return $this->versions()->max('version_number') ?? 0;
    }
}
