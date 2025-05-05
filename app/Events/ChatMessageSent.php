<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ChatMessage;
use App\Models\User; // Added for type hinting if needed

class ChatMessageSent implements ShouldBroadcast // Implement ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatMessage $message; // Public property to hold the message

    /**
     * Create a new event instance.
     */
    public function __construct(ChatMessage $message)
    {
        $this->message = $message->load('user'); // Eager load sender info
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast on a private channel for the receiver
        // And also for the sender so their UI updates too
        $channels = [];
        if ($this->message->receiver_id) {
            $channels[] = new PrivateChannel('chat.' . $this->message->receiver_id);
        }
        if ($this->message->sender_id) {
            $channels[] = new PrivateChannel('chat.' . $this->message->sender_id);
        }

        // Remove duplicates if sender and receiver are the same (though unlikely in chat)
        return array_unique($channels);
    }

    /**
     * The event's broadcast name.
     *
     * Use this in the Echo listener.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'chat-message'; // Specific name for frontend listener
    }

    /**
     * Get the data to broadcast.
     *
     * By default, all public properties are broadcast.
     * This customizes the data format to ensure consistency.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return ['message' => $this->message->toArray()];
    }
}