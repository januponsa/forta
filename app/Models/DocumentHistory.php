<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentHistory extends Model
{
    use HasFactory;

    protected $table = 'document_histories';

    protected $fillable = [
        'target_type', 'target_id',
        'action', 'description',
        'before_state', 'after_state',
        'template_version_id', 'asset_version_id', 'document_instance_id',
        'reason', 'user_id', 'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'before_state' => 'array',
            'after_state' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForTarget($query, string $type, int $id)
    {
        return $query->where('target_type', $type)->where('target_id', $id);
    }
}
