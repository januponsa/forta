<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    protected $fillable = [
        'label',
        'type',
        'category',
        'description',
        'placeholder',
        'options',
        'validation_rules',
        'is_required',
        'max_files',
        'max_size_mb',
        'allowed_types',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'validation_rules' => 'array',
            'allowed_types' => 'array',
            'is_required' => 'boolean',
        ];
    }
}
