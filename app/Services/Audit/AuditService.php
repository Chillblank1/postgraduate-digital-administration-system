<?php

namespace App\Services\Audit;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    public function log(User $actor, Model|string $entity, string $action, ?string $ipAddress = null): void
    {
        $type = is_string($entity) ? $entity : $entity::class;
        $id = is_string($entity) ? 0 : (int) $entity->getKey();

        AuditLog::query()->create([
            'actor_id' => $actor->id,
            'entity_type' => class_basename($type),
            'entity_id' => $id,
            'action' => $action,
            'ip_address' => $ipAddress,
            'created_at' => now(),
        ]);
    }
}
