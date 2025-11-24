<?php
declare(strict_types=1);

namespace App\Message\Domain\Events;

use App\Message\Domain\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;

class MessageSent implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets;

    public string $senderId;
    public string $receiverId;
    public array $payload;

    /** Create a new event instance. */
    public function __construct(Message $message)
    {
        $this->senderId = $message->getSenderId()->getValue();
        $this->receiverId = $message->getReceiverId()->getValue();
        $this->payload = $message->serialize();
    }

    public function broadcastOn(): array
    {
        $participants = [
            $this->senderId,
            $this->receiverId,
        ];

        sort($participants);

        $channelName = 'conversation.' . implode('.', $participants);

        return [
            new PrivateChannel($channelName),
            new PrivateChannel('user.' . $this->receiverId),
        ];
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
