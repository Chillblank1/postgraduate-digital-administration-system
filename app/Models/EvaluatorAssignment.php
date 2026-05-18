<?php

namespace App\Models;

use App\Enums\EaStatus;
use App\Enums\EvaluatorType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluatorAssignment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'submission_id',
        'evaluator_id',
        'evaluator_type',
        'assigned_at',
        'deadline',
        'status',
        'assigned_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'evaluator_type' => EvaluatorType::class,
            'assigned_at' => 'datetime',
            'deadline' => 'datetime',
            'status' => EaStatus::class,
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

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
