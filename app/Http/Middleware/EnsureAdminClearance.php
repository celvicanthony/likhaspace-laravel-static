<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminClearance
{
    public function handle(Request $request, Closure $next): Response
    {
        $clearedAt = $request->session()->get('admin_clearance_at');

        if (! is_numeric($clearedAt) || now()->timestamp - (int) $clearedAt > 1800) {
            $request->session()->forget('admin_clearance_at');

            return redirect()->route('admin.vault.gate')
                ->with('status', 'Enter the master clearance PIN to manage platform accounts.');
        }

        return $next($request);
    }
}
