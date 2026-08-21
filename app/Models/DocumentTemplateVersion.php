<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTemplateVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_template_id', 'version_number', 'status',
        'header_html', 'body_html', 'footer_html',
        'canvas_layout',
        'paper_size', 'orientation',
        'margin_top', 'margin_bottom', 'margin_left', 'margin_right',
        'letterhead_version_id', 'signatory_config',
        'change_notes', 'created_by', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'canvas_layout' => 'array',
            'signatory_config' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function template()
    {
        return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
    }

    public function letterheadVersion()
    {
        return $this->belongsTo(LetterheadVersion::class);
    }

    public function elements()
    {
        return $this->hasMany(DocumentElement::class, 'template_version_id')
                    ->orderBy('page_number')
                    ->orderBy('display_order');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function instances()
    {
        return $this->hasMany(DocumentInstance::class, 'template_version_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
