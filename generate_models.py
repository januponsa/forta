import os

models_dir = r"c:\Users\userJ\Documents\fortain\app\Models"
namespace = "namespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\SoftDeletes;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;\nuse Illuminate\Database\Eloquent\Relations\HasMany;\nuse Illuminate\Database\Eloquent\Relations\HasOne;\n"

models = {
    "DefenseCase": """
class DefenseCase extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $casts = [
        'metadata' => 'array',
        'finalized_at' => 'datetime'
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(DefenseSchedule::class);
    }
    
    public function latestSchedule(): HasOne
    {
        return $this->hasOne(DefenseSchedule::class)->latestOfMany();
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(DefenseAssignment::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function suggestions(): HasMany
    {
        return $this->hasMany(DefenseSuggestion::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(GeneratedDocument::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(DefenseHistory::class);
    }
}
""",
    "DefenseSchedule": """
class DefenseSchedule extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function defenseCase(): BelongsTo
    {
        return $this->belongsTo(DefenseCase::class);
    }
}
""",
    "DefenseAssignment": """
class DefenseAssignment extends Model
{
    protected $guarded = ['id'];

    public function defenseCase(): BelongsTo
    {
        return $this->belongsTo(DefenseCase::class);
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }
}
""",
    "RubricVersion": """
class RubricVersion extends Model
{
    protected $guarded = ['id'];
    
    public function sections(): HasMany
    {
        return $this->hasMany(RubricSection::class)->orderBy('display_order');
    }
}
""",
    "RubricSection": """
class RubricSection extends Model
{
    protected $guarded = ['id'];
    
    public function version(): BelongsTo
    {
        return $this->belongsTo(RubricVersion::class, 'rubric_version_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RubricItem::class)->orderBy('display_order');
    }
}
""",
    "RubricItem": """
class RubricItem extends Model
{
    protected $guarded = ['id'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(RubricSection::class, 'rubric_section_id');
    }
}
""",
    "Assessment": """
class Assessment extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'finalized_at' => 'datetime'
    ];

    public function defenseCase(): BelongsTo
    {
        return $this->belongsTo(DefenseCase::class);
    }

    public function rubricVersion(): BelongsTo
    {
        return $this->belongsTo(RubricVersion::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(AssessmentScore::class);
    }
}
""",
    "AssessmentScore": """
class AssessmentScore extends Model
{
    protected $guarded = ['id'];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function rubricItem(): BelongsTo
    {
        return $this->belongsTo(RubricItem::class);
    }
}
""",
    "DefenseSuggestion": """
class DefenseSuggestion extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'verified_at' => 'datetime'
    ];

    public function defenseCase(): BelongsTo
    {
        return $this->belongsTo(DefenseCase::class);
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }
}
""",
    "GeneratedDocument": """
class GeneratedDocument extends Model
{
    protected $guarded = ['id'];

    public function defenseCase(): BelongsTo
    {
        return $this->belongsTo(DefenseCase::class);
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
""",
    "DefenseHistory": """
class DefenseHistory extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'before_state' => 'array',
        'after_state' => 'array'
    ];

    public function defenseCase(): BelongsTo
    {
        return $this->belongsTo(DefenseCase::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
"""
}

for model_name, model_code in models.items():
    file_path = os.path.join(models_dir, f"{model_name}.php")
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write("<?php\n\n" + namespace + model_code)

print("Models generated successfully!")
