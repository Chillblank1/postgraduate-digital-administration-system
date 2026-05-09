<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;

class WorkflowTransition extends Model
{
    protected $fillable = [
        'from_status',
        'to_status',
        'allowed_role',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'from_status' => SubmissionStatus::class,
            'to_status' => SubmissionStatus::class,
            'allowed_role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
