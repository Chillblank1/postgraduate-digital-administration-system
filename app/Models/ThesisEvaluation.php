<?php

namespace App\Models;

use App\Enums\EvalStatus;
use App\Enums\EvaluatorType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThesisEvaluation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'submission_id',
        'evaluator_id',
        'evaluator_type',
        'scientific_field_relevance',
        'aims_objectives_hypothesis',
        'chapter_assessment',
        'overall_judgment',
        'intellectual_merit_score',
        'intellectual_merit_comments',
        'scientific_merit_score',
        'scientific_merit_comments',
        'results_quality_score',
        'results_comments',
        'presentation_score',
        'presentation_comments',
        'creativity_score',
        'creativity_comments',
        'total_marks',
        'percentage',
        'recommendation',
        'distinction_objection',
        'disclosure_permission',
        'sections_to_share',
        'status',
        'signed_at',
        'created_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'evaluator_type' => EvaluatorType::class,
            'distinction_objection' => 'boolean',
            'disclosure_permission' => 'boolean',
            'status' => EvalStatus::class,
            'signed_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
