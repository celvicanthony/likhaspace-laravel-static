<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->orderBy('id')
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->display_name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => ucfirst($user->status),
                'joined' => $user->created_at?->format('M d, Y') ?? '—',
            ])
            ->all();

        return view('admin.users', compact('users'));
    }

    public function update(Request $request, int $user): RedirectResponse
    {
        $target = User::findOrFail($user);
        $validated = $request->validate([
            'action' => ['required', 'in:role,status,delete'],
            'role' => ['required_if:action,role', 'nullable', 'in:client,artist'],
        ]);

        if ($target->is(Auth::user())) {
            return back()->withErrors([
                'user' => 'You cannot modify your own administrator account.',
            ]);
        }

        if ($validated['action'] === 'role') {
            $target->role = $validated['role'];

            if ($validated['role'] === 'client') {
                $target->artist_id = null;
            } elseif (! $target->artist_id) {
                $target->artist_id = Str::slug($target->name).'-'.Str::lower(Str::random(8));
            }

            $target->save();
        } elseif ($validated['action'] === 'status') {
            $target->status = $target->status === 'active' ? 'suspended' : 'active';
            $target->save();
        } else {
            $target->delete();
        }

        return back()->with('status', 'User account updated.');
    }
}
