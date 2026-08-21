<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'actor_id',
        'actor_role',
        'action',
        'target_type',
        'target_id',
        'data_before',
        'data_after',
        'freed_bytes',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'data_before' => 'array',
            'data_after' => 'array',
            'freed_bytes' => 'integer',
        ];
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
