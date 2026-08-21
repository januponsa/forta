<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterheadVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'letterhead_master_id', 'version_number', 'status',
        'logo_asset_id',
        'header_html', 'header_layout',
        'header_height', 'separator_style', 'separator_width', 'separator_color',
        'footer_html', 'footer_layout', 'footer_height',
        'margin_top', 'margin_bottom', 'margin_left', 'margin_right',
        'change_notes', 'created_by', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'header_layout' => 'array',
            'footer_layout' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function master()
    {
        return $this->belongsTo(LetterheadMaster::class, 'letterhead_master_id');
    }

    public function logoAsset()
    {
        return $this->belongsTo(DocumentAsset::class, 'logo_asset_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
