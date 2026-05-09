<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use App\Enums\SubmissionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    /** @use HasFactory<\Database\Factories\SubmissionFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'supervisor_id',
        'co_supervisor_id',
        'type',
        'title',
        'academic_level',
        'status',
        'supervisor_feedback',
        'supervisor_decision',
        'supervisor_signed_at',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => SubmissionType::class,
            'status' => SubmissionStatus::class,
            'supervisor_signed_at' => 'datetime',
            'submitted_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function coSupervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'co_supervisor_id');
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(SubmissionStatusHistory::class)->orderByDesc('created_at');
    }

    public function transitionLogs(): HasMany
    {
        return $this->hasMany(WorkflowTransitionLog::class)->orderByDesc('created_at');
    }

    public function documentVersions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class)->orderByDesc('version_number');
    }
}
