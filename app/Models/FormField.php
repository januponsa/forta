<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    protected $fillable = [
        'form_id',
        'section_id',
        'label',
        'type',
        'description',
        'placeholder',
        'options',
        'validation_rules',
        'is_required',
        'is_active',
        'order',
        'max_files',
        'max_size_mb',
        'allowed_types',
        'default_value',
        'conditions',
        'name',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'validation_rules' => 'array',
            'allowed_types' => 'array',
            'conditions' => 'array',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function section()
    {
        return $this->belongsTo(FormSection::class, 'section_id');
    }

    public function submissionFiles()
    {
        return $this->hasMany(SubmissionFile::class, 'field_id');
    }
}
