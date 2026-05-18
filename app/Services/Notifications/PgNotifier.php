<?php

namespace App\Services\Notifications;

use App\Enums\UserRole;
use App\Models\DomainNotification;
use App\Models\User;

final class PgNotifier
{
    public function notifyUser(
        int $recipientUserId,
        string $title,
        string $body,
        ?string $category = null,
    ): DomainNotification {
        return DomainNotification::query()->create([
            'user_id' => $recipientUserId,
            'type' => $category ?? 'hod',
            'message' => trim($title.': '.$body),
            'created_at' => now(),
        ]);
    }

    /** @return list<int> recipient user ids */
    public function notifyRole(UserRole $role, string $title, string $body, ?string $category = null): array
    {
        $recipientIds = [];

        User::query()
            ->where('role', $role)
            ->pluck('id')
            ->each(function (int $userId) use ($title, $body, $category, &$recipientIds): void {
                $this->notifyUser($userId, $title, $body, $category);
                $recipientIds[] = $userId;
            });

        return $recipientIds;
    }
}
