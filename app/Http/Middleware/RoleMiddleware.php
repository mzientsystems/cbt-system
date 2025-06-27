<?php
// app/Http/Middleware/RoleMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        if (!$user->isActive()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Account is not active.');
        }

        if ($user->isLocked()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Account is temporarily locked.');
        }

        if ($user->needsPasswordChange()) {
            return redirect()->route('password.change');
        }

        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized access.');
    }
}