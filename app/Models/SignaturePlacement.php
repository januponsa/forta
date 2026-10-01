<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignaturePlacement extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'include_stamp' => 'boolean',
    ];

    public function request() { return $this->belongsTo(SignatureRequest::class, 'signature_request_id'); }
    public function lecturer() { return $this->belongsTo(Lecturer::class); }
}
