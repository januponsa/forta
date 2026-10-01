<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterLogbook extends Model
{
    use HasFactory;

    protected $fillable = [
        'letter_number',
        'letter_title',
        'recipient_name',
        'letter_date',
        'is_backdated',
        'source_type',
        'source_id',
        'final_pdf_path',
        'created_by',
    ];

    protected $casts = [
        'letter_date' => 'date',
        'is_backdated' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
