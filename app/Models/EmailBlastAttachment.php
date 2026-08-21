<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailBlastAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_blast_id',
        'file_path',
        'file_name',
        'file_size',
        'mime_type'
    ];

    public function emailBlast()
    {
        return $this->belongsTo(EmailBlast::class);
    }
}
