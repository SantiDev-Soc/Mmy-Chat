<?php
declare(strict_types=1);

namespace App\Message\Application\TransformerDTO;

use App\Message\Application\DTO\MessageResponseDto;
use App\Message\Domain\Message;
use App\Shared\Application\InterfaceDto\TransformerToDtoInterface;

class TransformerDto implements TransformerToDtoInterface
{
    public function transform(Message $message): MessageResponseDto
    {
        return new MessageResponseDto(
            $message->getId()->getValue(),
            $message->getContent(),
            $message->getSenderId()->getValue(),
            $message->getReceiverId()->getValue(),
            $message->getSentAt()->format('c'),
            $message->getReadAt()?->format('c'),
            $message->getCreatedAt()->format('c'),
            $message->getUpdatedAt()->format('c'),
        );

    }

}
