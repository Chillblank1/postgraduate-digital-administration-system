<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsHeadOfDepartment
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || $user->role !== UserRole::Hod) {
            abort(Response::HTTP_FORBIDDEN, 'Only Heads of Department may access this area.');
        }

        $departmentName = trim((string) ($user->department ?? ''));

        if ($departmentName === '') {
            abort(Response::HTTP_FORBIDDEN, 'HoD user has no department set on their profile.');
        }

        $request->attributes->set('hod_department_name', $departmentName);

        return $next($request);
    }
}
