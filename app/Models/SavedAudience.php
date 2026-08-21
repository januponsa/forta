<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedAudience extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'filter_criteria',
        'created_by'
    ];

    protected $casts = [
        'filter_criteria' => 'array',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
