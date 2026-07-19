<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->paginate(20);
        return view('owner.notifications.index', compact('notifications'));
    }

    public function unread(Request $request)
    {
        // Return recent notifications (read AND unread) so reading them does not
        // remove them from the dropdown; the unread badge is derived client-side.
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->take(15)
            ->get()
            ->map(fn($n) => [
                'id'               => $n->id,
                'data'             => $n->data,
                'read_at'          => $n->read_at,
                'created_at_human' => $n->created_at->diffForHumans(),
            ]);

        return response()->json($notifications);
    }

    public function read(Request $request, string $id)
    {
        $request->user()
            ->notifications()
            ->where('id', $id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }
}