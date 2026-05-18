<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserStatusNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    private const PER_PAGE = 4;

    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $term = '%'.$request->string('search').'%';
                $q->where('first_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            }))
            ->when($request->filled('role'),   fn ($q) => $q->where('role',   $request->string('role')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function show(User $user): JsonResponse
    {
        return response()->json($user);
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $user->status = $user->status->toggle();
        $user->save();

        // ↓ Notify the user about their new status
        $user->notify(new UserStatusNotification(
            $user->status->value  // passes 'blocked' or 'active'/'approved'
        ));

        return back()->with('success', 'User status updated.');
    }
}