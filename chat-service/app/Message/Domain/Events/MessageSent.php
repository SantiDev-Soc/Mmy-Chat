<?php
declare(strict_types=1);

namespace App\Message\Domain\Events;

use App\Message\Domain\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    /** Create a new event instance.*/
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        $participants = [
            $this->message->getSenderId()->getValue(),
            $this->message->getReceiverId()->getValue(),
        ];

        sort($participants);

        $channelName = 'conversation.' . implode('.', $participants);

        return [new PrivateChannel($channelName)];
    }

    public function broadcastWith(): array
    {
        return $this->message->serialize();
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
