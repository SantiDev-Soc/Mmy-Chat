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
    use Dispatchable, InteractsWithSockets;
    // NOTA: He quitado SerializesModels porque 'Message' es de Doctrine, no Eloquent.

    // Guardamos datos primitivos para evitar errores de serialización en la cola
    public string $senderId;
    public string $receiverId;
    public array $payload;

    /** Create a new event instance. */
    public function __construct(Message $message)
    {
        // Extraemos los datos AQUÍ, antes de que el evento se vaya a la cola
        $this->senderId = $message->getSenderId()->getValue();
        $this->receiverId = $message->getReceiverId()->getValue();

        // Serializamos el mensaje ahora para tener el array listo para enviar
        $this->payload = $message->serialize();
    }

    public function broadcastOn(): array
    {
        // Usamos los strings guardados
        $participants = [
            $this->senderId,
            $this->receiverId,
        ];

        //ordenamos los messages de los participantes
        sort($participants);

        // Canal ejemplo: conversation.uuid-1.uuid-2
        $channelName = 'conversation.' . implode('.', $participants);

        return [new PrivateChannel($channelName)];
    }

    public function broadcastWith(): array
    {
        // Devolvemos el array que preparamos en el constructor
        return $this->payload;
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
