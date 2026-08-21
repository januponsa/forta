<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentAsset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'asset_type', 'mime_type', 'status',
        'owner_type', 'owner_id',
        'default_width', 'default_height',
        'active_version_id', 'created_by',
    ];

    public function versions()
    {
        return $this->hasMany(DocumentAssetVersion::class);
    }

    public function activeVersion()
    {
        return $this->belongsTo(DocumentAssetVersion::class, 'active_version_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the owning model (User or Lecturer).
     */
    public function owner()
    {
        return $this->morphTo();
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('asset_type', $type);
    }

    public function scopeOwnedBy($query, string $ownerType, int $ownerId)
    {
        return $query->where('owner_type', $ownerType)->where('owner_id', $ownerId);
    }
}
