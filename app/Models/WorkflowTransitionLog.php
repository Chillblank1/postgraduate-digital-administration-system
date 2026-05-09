<?php

namespace App\Models;

use App\Enums\WorkflowExecutionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowTransitionLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'submission_id',
        'transition_id',
        'executed_by',
        'execution_status',
        'error_message',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'execution_status' => WorkflowExecutionStatus::class,
            'created_at' => 'datetime',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function transition(): BelongsTo
    {
        return $this->belongsTo(WorkflowTransition::class, 'transition_id');
    }

    public function executor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by');
    }
}
