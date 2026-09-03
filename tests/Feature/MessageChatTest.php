<?php

namespace Tests\Feature;

use App\Enums\ListingStatus;
use App\Models\Conversation;
use App\Models\Location;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MessageChatTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $customer;

    private Conversation $conversation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::create([
            'role' => 'owner', 'first_name' => 'O', 'last_name' => 'Wner',
            'email' => 'owner'.uniqid().'@example.com', 'phone' => '03001234567',
            'password' => bcrypt('password'),
        ]);

        $this->customer = User::create([
            'role' => 'customer', 'first_name' => 'C', 'last_name' => 'Ust',
            'email' => 'cust'.uniqid().'@example.com', 'phone' => '03007654321',
            'password' => bcrypt('password'),
        ]);

        $location = Location::create([
            'user_id' => $this->owner->id, 'title' => 'Studio', 'description' => 'd',
            'address' => 'a', 'city' => 'Lahore', 'category' => 'Studio',
            'price_per_hour' => 1300, 'status' => ListingStatus::Approved,
        ]);

        $this->conversation = Conversation::create([
            'customer_id' => $this->customer->id,
            'owner_id'    => $this->owner->id,
            'location_id' => $location->id,
        ]);
    }

    private function message(User $sender, array $overrides = []): Message
    {
        return Message::create(array_merge([
            'conversation_id' => $this->conversation->id,
            'sender_id'       => $sender->id,
            'body'            => 'Hello there',
        ], $overrides));
    }

    public function test_sender_can_edit_their_own_message(): void
    {
        $message = $this->message($this->customer);
        $sentAt = $message->created_at;

        // Timestamps are second-granular, so jump ahead to make the
        // updated_at bump (and the "(edited)" hint it drives) observable.
        $this->travel(30)->seconds();

        $this->actingAs($this->customer)
            ->put(route('customer.messages.update', $message), ['body' => 'Updated text'])
            ->assertRedirect(route('customer.messages', $this->conversation));

        $message->refresh();

        $this->assertSame('Updated text', $message->body);
        $this->assertTrue($message->created_at->equalTo($sentAt));
        $this->assertTrue($message->updated_at->gt($sentAt));
    }

    public function test_marking_messages_read_does_not_flag_them_as_edited(): void
    {
        $message = $this->message($this->owner);
        $sentAt = $message->created_at;

        $this->travel(30)->seconds();

        $this->actingAs($this->customer)
            ->get(route('customer.messages', $this->conversation))
            ->assertOk();

        $message->refresh();

        // A read receipt must not look like an edit: updated_at stays put,
        // so the view's "(edited)" hint (updated_at > created_at) never fires.
        $this->assertNotNull($message->read_at);
        $this->assertTrue($message->updated_at->equalTo($sentAt));

        // Second render reads the DB state - a bumped updated_at would show
        // "edited" here even though the message was never edited.
        $this->actingAs($this->customer)
            ->get(route('customer.messages', $this->conversation))
            ->assertOk()
            ->assertDontSee('edited');
    }

    public function test_other_participant_cannot_edit_or_delete_a_message(): void
    {
        $message = $this->message($this->customer);

        $this->actingAs($this->owner)
            ->put(route('owner.messages.update', $message), ['body' => 'Hijacked'])
            ->assertForbidden();

        $this->actingAs($this->owner)
            ->delete(route('owner.messages.destroy', $message))
            ->assertForbidden();

        $this->assertSame('Hello there', $message->fresh()->body);
        $this->assertDatabaseHas('messages', ['id' => $message->id]);
    }

    public function test_non_participant_cannot_edit_or_delete_a_message(): void
    {
        $intruder = User::create([
            'role' => 'customer', 'first_name' => 'I', 'last_name' => 'Ntruder',
            'email' => 'intruder'.uniqid().'@example.com', 'phone' => '03009999999',
            'password' => bcrypt('password'),
        ]);

        $message = $this->message($this->customer);

        $this->actingAs($intruder)
            ->put(route('customer.messages.update', $message), ['body' => 'Hijacked'])
            ->assertForbidden();

        $this->actingAs($intruder)
            ->delete(route('customer.messages.destroy', $message))
            ->assertForbidden();

        $this->assertSame('Hello there', $message->fresh()->body);
        $this->assertDatabaseHas('messages', ['id' => $message->id]);
    }

    public function test_delete_removes_the_message_and_its_attachment_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('message-attachments/a.jpg', 'x');

        $message = $this->message($this->customer, [
            'body'            => '',
            'attachment_path' => 'message-attachments/a.jpg',
            'attachment_type' => 'image',
            'attachment_name' => 'a.jpg',
        ]);

        $this->actingAs($this->customer)
            ->delete(route('customer.messages.destroy', $message))
            ->assertRedirect(route('customer.messages', $this->conversation));

        $this->assertDatabaseMissing('messages', ['id' => $message->id]);
        Storage::disk('public')->assertMissing('message-attachments/a.jpg');
    }

    public function test_update_with_empty_body_is_rejected_when_there_is_no_attachment(): void
    {
        $message = $this->message($this->customer);

        $this->actingAs($this->customer)
            ->put(route('customer.messages.update', $message), ['body' => ''])
            ->assertSessionHasErrors('body');

        $this->assertSame('Hello there', $message->fresh()->body);
    }

    public function test_update_may_clear_the_caption_of_a_message_with_an_attachment(): void
    {
        $message = $this->message($this->customer, ['attachment_path' => 'message-attachments/a.jpg']);

        $this->actingAs($this->customer)
            ->put(route('customer.messages.update', $message), ['body' => ''])
            ->assertRedirect(route('customer.messages', $this->conversation));

        $this->assertSame('', $message->fresh()->body);
    }

    public function test_duplicate_message_token_only_creates_one_message(): void
    {
        $this->actingAs($this->customer)
            ->post(route('customer.messages.store', $this->conversation), [
                'body' => 'hi', 'message_token' => 'tok-1',
            ])
            ->assertRedirect(route('customer.messages', $this->conversation));

        // Double-submit of the same rendered form: same token, so the second
        // request must not create another message.
        $this->actingAs($this->customer)
            ->post(route('customer.messages.store', $this->conversation), [
                'body' => 'hi', 'message_token' => 'tok-1',
            ])
            ->assertRedirect(route('customer.messages', $this->conversation));

        $this->assertSame(1, Message::count());
    }

    public function test_distinct_tokens_create_distinct_messages(): void
    {
        $this->actingAs($this->customer)
            ->post(route('customer.messages.store', $this->conversation), [
                'body' => 'hi', 'message_token' => 'tok-1',
            ]);

        // A fresh render mints a fresh token, so a legitimate second message
        // with the same body goes through.
        $this->actingAs($this->customer)
            ->post(route('customer.messages.store', $this->conversation), [
                'body' => 'hi', 'message_token' => 'tok-2',
            ]);

        $this->assertSame(2, Message::count());
    }

    public function test_only_your_own_messages_render_the_action_menu(): void
    {
        // Each message's ⋮ menu lives in the row itself, so permission is
        // asserted on the wiring: only own messages hand their id to
        // toggleMenu and carry the menu container.
        $ownerMessage = $this->message($this->owner);

        $this->actingAs($this->customer)
            ->get(route('customer.messages', $this->conversation))
            ->assertOk()
            ->assertSee('name="message_token"', false)
            ->assertDontSee('toggleMenu('.$ownerMessage->id.')', false);

        $myMessage = $this->message($this->customer);

        $this->actingAs($this->customer)
            ->get(route('customer.messages', $this->conversation))
            ->assertOk()
            ->assertSee('toggleMenu('.$myMessage->id.')', false)
            ->assertSee('id="message-menu-'.$myMessage->id.'"', false);
    }
}
