<?php
declare(strict_types=1);

namespace App\Message\Domain\Events;

use App\Message\Domain\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

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
           strtolower($this->senderId),
            strtolower($this->receiverId),
        ];

        sort($participants);

        $channelName = 'conversation.' .implode('.', $participants);

        return [
            new PrivateChannel($channelName),
            new PrivateChannel('user.' . strtolower($this->receiverId)),
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
