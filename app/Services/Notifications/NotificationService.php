<?php

namespace App\Services\Notifications;

use App\Models\DomainNotification;
use App\Models\User;

class NotificationService
{
    public function notify(User $recipient, string $type, string $message): DomainNotification
    {
        return DomainNotification::query()->create([
            'user_id' => $recipient->id,
            'type' => $type,
            'message' => $message,
            'read_at' => null,
            'created_at' => now(),
        ]);
    }
}
