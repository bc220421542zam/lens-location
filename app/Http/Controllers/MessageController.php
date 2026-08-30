<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Conversation;
use App\Notifications\NewMessageNotification;
use App\Models\Location;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

            // Mark the other party's messages read. toBase() skips Eloquent's
            // updated_at bump: a read receipt is not an edit, and the views
            // derive the "(edited)" hint from updated_at > created_at.
            $conversation->messages()
                ->where('sender_id', '!=', Auth::id())
                ->whereNull('read_at')
                ->toBase()
                ->update(['read_at' => now()]);
        }

        $viewName = $user->hasRole(Role::Owner) ? 'owner.messages' : 'customer.messages';

        // One-time token proving this composer form was rendered fresh. A
        // double-submitted form carries the same token, so the duplicate is
        // dropped in store() instead of creating a second message.
        $messageToken = (string) Str::uuid();

        return view($viewName, compact('conversations', 'conversation', 'messageToken'));
    }

    public function store(Request $request, Conversation $conversation): RedirectResponse
    {
        $this->authorizeParticipant($conversation);

        $request->validate([
            'message_token' => 'required|string|max:255',
        ]);

        $token = $request->string('message_token')->toString();

        // Duplicate submit of the same rendered form (double click, Enter +
        // click, back/refresh resubmit). This assumes a server-side session
        // store - PHP's file handler and Laravel's database handler both
        // serialize requests on the same session, so the check is race-free.
        if (in_array($token, $request->session()->get('used_message_tokens', []), true)) {
            return redirect()->route($this->messagesRoute(), $conversation->id);
        }

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

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'body' => $request->string('body'),
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'attachment_name' => $attachmentName,
        ]);

        // Notify the other participant so their Messages nav shows unseen
        // activity. Attachments without a caption still deserve a preview.
        $recipient = $conversation->customer_id === Auth::id()
            ? $conversation->owner
            : $conversation->customer;

        if ($recipient) {
            $preview = $message->body
                ? Str::limit($message->body, 40)
                : ($message->attachment_name ? '📎 '.$message->attachment_name : '📎 Attachment');

            $recipient->notify(new NewMessageNotification(
                Auth::user()->first_name ?: 'Someone',
                $preview,
            ));
        }

        $conversation->touch();

        // Burn the token only after a successful create, so a validation-error
        // redirect (which re-renders a fresh form with a fresh token anyway)
        // never deadlocks a legitimate resubmit.
        $used = $request->session()->get('used_message_tokens', []);
        $used[] = $token;
        $request->session()->put('used_message_tokens', array_slice($used, -10));

        return redirect()->route($this->messagesRoute(), $conversation->id);
    }

    public function update(Request $request, Message $message): RedirectResponse
    {
        $this->authorizeSender($message);

        $request->validate([
            'body' => 'nullable|string|max:1000',
        ]);

        // Attachments are not editable; an emptied body is only rejected when
        // there is nothing else left to display.
        if (! $request->filled('body') && ! $message->attachment_path) {
            return back()->withErrors(['body' => 'Message cannot be empty.']);
        }

        $message->update(['body' => $request->string('body')]);

        $message->conversation->touch();

        return redirect()->route($this->messagesRoute(), $message->conversation_id);
    }

    public function destroy(Message $message): RedirectResponse
    {
        $this->authorizeSender($message);

        $conversation = $message->conversation;

        if ($message->attachment_path) {
            Storage::disk('public')->delete($message->attachment_path);
        }

        $message->delete();

        $conversation->touch();

        return redirect()->route($this->messagesRoute(), $conversation->id);
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

    private function authorizeSender(Message $message): void
    {
        abort_unless($message->sender_id === Auth::id(), 403);

        $this->authorizeParticipant($message->conversation);
    }

    private function messagesRoute(): string
    {
        return Auth::user()->hasRole(Role::Owner) ? 'owner.messages' : 'customer.messages';
    }
}