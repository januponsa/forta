<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentAssetVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_asset_id', 'version_number',
        'original_path', 'processed_path', 'thumbnail_path',
        'original_width', 'original_height', 'aspect_ratio',
        'file_size', 'file_format', 'has_transparency',
        'crop_data', 'rotation', 'flip_horizontal', 'flip_vertical',
        'opacity', 'object_fit',
        'processed_width', 'processed_height',
        'change_notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'crop_data' => 'array',
            'has_transparency' => 'boolean',
            'flip_horizontal' => 'boolean',
            'flip_vertical' => 'boolean',
        ];
    }

    public function asset()
    {
        return $this->belongsTo(DocumentAsset::class, 'document_asset_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
