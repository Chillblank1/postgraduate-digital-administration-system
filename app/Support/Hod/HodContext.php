<?php

namespace App\Support\Hod;

use App\Enums\UserRole;
use Illuminate\Http\Request;

final class HodContext
{
    /** PDAS stores department on users as a string (not department_id). */
    public static function departmentName(Request $request): string
    {
        $cached = $request->attributes->get('hod_department_name');

        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $user = $request->user();
        abort_if($user === null || $user->role !== UserRole::Hod, 403);

        $name = trim((string) ($user->department ?? ''));
        abort_if($name === '', 403, 'HoD user has no department set.');

        $request->attributes->set('hod_department_name', $name);

        return $name;
    }
}
