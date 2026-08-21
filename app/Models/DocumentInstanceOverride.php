<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentInstanceOverride extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_instance_id',
        'field_key', 'original_value', 'override_value',
        'override_type', 'reason', 'overridden_by',
    ];

    public function instance()
    {
        return $this->belongsTo(DocumentInstance::class, 'document_instance_id');
    }

    public function overriddenBy()
    {
        return $this->belongsTo(User::class, 'overridden_by');
    }
}
