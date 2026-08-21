<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentInstance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'document_template_id', 'template_version_id', 'letterhead_version_id',
        'source_type', 'source_id',
        'status', 'data_snapshot', 'asset_snapshots', 'override_data',
        'file_path', 'file_checksum',
        'finalized_at', 'finalized_by',
        'previous_instance_id', 'revision_number',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'data_snapshot' => 'array',
            'asset_snapshots' => 'array',
            'override_data' => 'array',
            'finalized_at' => 'datetime',
        ];
    }

    public function template()
    {
        return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
    }

    public function templateVersion()
    {
        return $this->belongsTo(DocumentTemplateVersion::class, 'template_version_id');
    }

    public function letterheadVersion()
    {
        return $this->belongsTo(LetterheadVersion::class);
    }

    public function overrides()
    {
        return $this->hasMany(DocumentInstanceOverride::class);
    }

    public function previousInstance()
    {
        return $this->belongsTo(DocumentInstance::class, 'previous_instance_id');
    }

    public function revisions()
    {
        return $this->hasMany(DocumentInstance::class, 'previous_instance_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function finalizer()
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    /**
     * Get the source model (InternshipLetterRequest, DefenseCase, etc.)
     */
    public function source()
    {
        return $this->morphTo();
    }

    public function scopeFinal($query)
    {
        return $query->where('status', 'final');
    }
}
