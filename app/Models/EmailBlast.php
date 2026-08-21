<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailBlast extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'body_html',
        'delivery_mode',
        'target_description',
        'total_recipients',
        'status',
        'created_by',
        'sent_by',
        'scheduled_at',
        'sent_at'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function recipients()
    {
        return $this->hasMany(EmailBlastRecipient::class);
    }

    public function attachments()
    {
        return $this->hasMany(EmailBlastAttachment::class);
    }
}
