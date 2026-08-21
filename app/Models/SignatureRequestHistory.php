<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignatureRequestHistory extends Model
{
    use HasFactory;
    
    protected $fillable = ['signature_request_id', 'user_id', 'action', 'note'];

    public function signatureRequest() { return $this->belongsTo(SignatureRequest::class); }
    public function user() { return $this->belongsTo(User::class); }
}
