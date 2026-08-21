<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormSection extends Model
{
    protected $fillable = [
        'form_id',
        'title',
        'description',
        'order',
        'section_code',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function fields()
    {
        return $this->hasMany(FormField::class, 'section_id')->orderBy('order');
    }
}
