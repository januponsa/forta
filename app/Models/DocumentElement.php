<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentElement extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_version_id', 'element_type', 'page_number',
        'x', 'y', 'width', 'height', 'rotation', 'opacity',
        'text_align', 'padding', 'margin_el', 'border', 'z_index',
        'locked', 'visible', 'condition',
        'content', 'placeholder_key', 'properties',
        'asset_id', 'asset_version_id',
        'data_source', 'table_columns',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'table_columns' => 'array',
            'locked' => 'boolean',
            'visible' => 'boolean',
        ];
    }

    public function templateVersion()
    {
        return $this->belongsTo(DocumentTemplateVersion::class, 'template_version_id');
    }

    public function asset()
    {
        return $this->belongsTo(DocumentAsset::class);
    }

    public function assetVersion()
    {
        return $this->belongsTo(DocumentAssetVersion::class);
    }
}
