<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'slug',
        'activity_type_id',
        'phase',
        'semester',
        'open_at',
        'close_at',
        'status',
        'form_code',
        'depends_on_form_id',
        'display_order',
        'version',
        'parent_form_id',
    ];

    protected function casts(): array
    {
        return [
            'open_at' => 'datetime',
            'close_at' => 'datetime',
        ];
    }

    public function activityType()
    {
        return $this->belongsTo(ActivityType::class);
    }

    public function fields()
    {
        return $this->hasMany(FormField::class)->orderBy('order');
    }

    public function sections()
    {
        return $this->hasMany(FormSection::class)->orderBy('order');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function academicCalendarEvents()
    {
        return $this->hasMany(AcademicCalendarEvent::class);
    }

    public function parentForm()
    {
        return $this->belongsTo(Form::class, 'parent_form_id');
    }

    public function childForms()
    {
        return $this->hasMany(Form::class, 'parent_form_id');
    }
}
