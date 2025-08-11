<?php

namespace App\Events;

use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $conversation;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Message  $message
     * @param  \App\Models\Conversation  $conversation
     * @return void
     */
    public function __construct(Message $message, Conversation $conversation)
    {
        $this->message = $message;
        $this->conversation = $conversation;

        // Eager load sender for client-side use
        $this->message->load('sender');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // Broadcast on a private channel specific to this conversation
        return new PrivateChannel('chat.conversation.' . $this->conversation->id);
    }

    /**
     * The event's broadcast name.
     * This is what your Echo listener will look for (e.g., .MessageSent)
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'MessageSent';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
                'sender_id' => $this->message->sender_id,
                'content' => $this->message->content,
                'read_at' => $this->message->read_at,
                'created_at' => $this->message->created_at->format('Y-m-d H:i:s'),
                'created_at_formatted' => $this->message->created_at->format('H:i A, M d'),
                'sender_name' => $this->message->sender->name, // Include sender name
            ],
        ];
    }
}