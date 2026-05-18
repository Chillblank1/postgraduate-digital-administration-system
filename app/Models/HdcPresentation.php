<?php

namespace App\Models;

use App\Enums\HdcOutcome;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HdcPresentation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'submission_id',
        'fpgcr_id',
        'scheduled_at',
        'venue',
        'outcome',
        'outcome_notes',
        'created_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'created_at' => 'datetime',
            'outcome' => HdcOutcome::class,
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function fpgcr(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fpgcr_id');
    }
}
