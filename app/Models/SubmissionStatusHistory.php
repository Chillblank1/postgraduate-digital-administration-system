<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionStatusHistory extends Model
{
    public $timestamps = false;

    protected $table = 'submission_status_history';

    protected $fillable = [
        'submission_id',
        'from_status',
        'to_status',
        'changed_by',
        'actor_role',
        'comments',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'from_status' => SubmissionStatus::class,
            'to_status' => SubmissionStatus::class,
            'actor_role' => UserRole::class,
            'created_at' => 'datetime',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
