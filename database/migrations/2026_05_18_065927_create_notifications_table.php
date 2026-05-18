<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Full page: view all notifications
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    // AJAX: return only unread notifications for the navbar bell
    public function unread(Request $request)
    {
        $notifications = $request->user()
            ->unreadNotifications()
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

    // AJAX: mark all as read
    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}