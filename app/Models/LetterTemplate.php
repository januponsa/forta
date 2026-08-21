<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'logo_path',
        'kop_text',
        'footer_text',
        'university_name',
        'campus_address',
        'contact_info',
        'city',
        'letter_code',
        'subject',
        'opening_paragraph',
        'purpose_paragraph',
        'closing_paragraph',
        'signatory_name',
        'signatory_position',
        'signature_path',
        'stamp_path',
        'number_format',
        'margin_top',
        'margin_bottom',
        'margin_left',
        'margin_right',
        'paper_size',
        'logo_asset_id',
        'lecturer_id',
    ];

    public function logoAsset()
    {
        return $this->belongsTo(DocumentAsset::class, 'logo_asset_id');
    }

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class, 'lecturer_id');
    }
}
