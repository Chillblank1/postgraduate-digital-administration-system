<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'job_title',
        'affiliation',
        'office_address',
        'postal_address',
        'bio',
        'profile_photo',
        'account_created_at',
        'source_system',
    ];

    protected function casts(): array
    {
        return [
            'account_created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
