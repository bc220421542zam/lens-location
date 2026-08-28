<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserStatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    private const PER_PAGE = 10;

    public function index(Request $request): View
    {
        $sort = $request->get('sort', 'first_name');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = ['first_name', 'email', 'role', 'status'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'first_name';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $users = User::query()
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $term = '%'.$request->string('search').'%';
                $q->where('first_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            }))
            ->when($request->filled('role'),   fn ($q) => $q->where('role',   $request->string('role')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->orderBy($sort, $direction)
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        $users->onEachSide(1);

        return view('admin.users', compact('users'));
    }

    public function show(User $user): View
    {
        return view('admin.users.show', [
            'user' => $user->loadCount(['locations', 'transactionsAsOwner']),
        ]);
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        // Read BEFORE toggling: active -> blocked requires a reason,
        // blocked -> active ignores it. The direction is derived server-side,
        // never trusted from the form, so a hand-crafted POST can't block
        // without one.
        $blocking = $user->status === UserStatus::Active;

        $data = $request->validate([
            'reason' => $blocking
                ? 'required|string|max:500'
                : 'nullable|string|max:500',
        ]);

        $user->update($blocking ? [
            'status'       => UserStatus::Blocked,
            'block_reason' => $data['reason'],
            'blocked_at'   => now(),
        ] : [
            'status'       => UserStatus::Active,
            'block_reason' => null,
            'blocked_at'   => null,
        ]);

        // Notify the user about their new status
        $user->notify(new UserStatusNotification(
            $user->status->value,
            $data['reason'] ?? null,
        ));

        return back()->with('success', $blocking ? 'User blocked.' : 'User activated.');
    }
}