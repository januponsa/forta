<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    protected $fillable = [
        'user_id',
        'nip',
        'name',
        'email',
        'is_active',
        'signature_path',
        'position',
        'stamp_path',
        'default_width',
        'default_height',
        'include_name',
        'include_position',
        'include_date',
        'allowed_roles'
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
