<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
 // app/Http/Middleware/RoleMiddleware.php

// public function handle($request, \Closure $next, $roleName)
// {
//     $user = $request->user();

//     if (!$user || !$user->hasRole($roleName)) {
//         return response()->json(['message' => 'ليس لديك صلاحية الوصول.'], 403);
//     }

//     return $next($request);
// }
public function handle($request, \Closure $next, ...$roles)
{
    $user = $request->user();

    if (!$user || !$user->role || !in_array($user->role->name, $roles)) {
        return response()->json(['message' => 'ليس لديك صلاحية الوصول.'], 403);
    }

    return $next($request);
}

}
