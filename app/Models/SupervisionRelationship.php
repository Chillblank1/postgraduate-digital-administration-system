<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupervisionRelationship extends Model
{
    protected $fillable = [
        'supervisor_id',
        'student_id',
        'co_supervisor_id',
        'assigned_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
        ];
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function coSupervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'co_supervisor_id');
    }
}
