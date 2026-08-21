<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderRecipient extends Model
{
    protected $fillable = [
        'name',
        'email',
        'role',
    ];
}
