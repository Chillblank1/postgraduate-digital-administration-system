<?php

namespace App\Models;

use App\Enums\EvalStatus;
use App\Enums\EvaluatorType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionEvaluation extends Model
{
    protected $fillable = [
        'submission_id',
        'evaluator_id',
        'evaluator_type',
        'grade',
        'checklist_signed',
        'checklist_signed_at',
        'notes',
        'status',
        'submitted_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'evaluator_type' => EvaluatorType::class,
            'checklist_signed' => 'boolean',
            'checklist_signed_at' => 'datetime',
            'status' => EvalStatus::class,
            'submitted_at' => 'datetime',
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
