<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            $route = $request->routeIs('admin.*') ? 'admin.login' : 'login';

            return redirect()->guest(route($route));
        }

        if ($user->status !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route($user->role === 'admin' ? 'admin.login' : 'login')
                ->withErrors(['email' => 'This account is currently suspended.']);
        }

        abort_unless(is_string($user->role) && in_array($user->role, $roles, true), 403);

        if ($user->role === 'artist' && $user->artist_id) {
            $request->session()->put('artist_id', $user->artist_id);
        } else {
            $request->session()->forget('artist_id');
        }

        return $next($request);
    }
}
