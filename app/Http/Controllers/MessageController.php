<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Conversation;
use App\Models\Location;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(?Conversation $conversation = null): View
    {
        $user = Auth::user();

        $conversations = Conversation::query()
            ->where('customer_id', $user->id)
            ->orWhere('owner_id', $user->id)
            ->with(['customer', 'owner', 'location', 'lastMessage'])
            ->latest('updated_at')
            ->get();

        if ($conversation) {
            $this->authorizeParticipant($conversation);

            $conversation->load('messages.sender');

            $conversation->messages()
                ->where('sender_id', '!=', Auth::id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        $viewName = $user->hasRole(Role::Owner) ? 'owner.messages' : 'customer.messages';

        return view($viewName, compact('conversations', 'conversation'));
    }

    public function store(Request $request, Conversation $conversation): RedirectResponse
    {
        $this->authorizeParticipant($conversation);

        $request->validate([
            'body' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xlsx',
        ]);

        if (! $request->filled('body') && ! $request->hasFile('attachment')) {
            return back()->withErrors(['body' => 'Message cannot be empty.']);
        }

        $attachmentPath = null;
        $attachmentType = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('message-attachments', 'public');
            $attachmentName = $file->getClientOriginalName();
            $attachmentType = str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'file';
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'body' => $request->string('body'),
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'attachment_name' => $attachmentName,
        ]);

        $conversation->touch();

        $routeName = Auth::user()->hasRole(Role::Owner) ? 'owner.messages' : 'customer.messages';

        return redirect()->route($routeName, $conversation->id);
    }

    public function startWithOwner(Location $location): RedirectResponse
    {
        $conversation = Conversation::firstOrCreate([
            'customer_id' => Auth::id(),
            'owner_id' => $location->user_id,
            'location_id' => $location->id,
        ]);

        return redirect()->route('customer.messages', $conversation->id);
    }

    private function authorizeParticipant(Conversation $conversation): void
    {
        abort_unless(
            in_array(Auth::id(), [$conversation->customer_id, $conversation->owner_id]),
            403
        );
    }
}