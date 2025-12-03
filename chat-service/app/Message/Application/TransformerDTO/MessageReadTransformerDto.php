<?php
declare(strict_types=1);

namespace App\Message\Application\TransformerDTO;

use App\Message\Application\DTO\MessageReadResponseDto;
use App\Message\Domain\MessageRead;
use App\Shared\Application\InterfaceDto\MessageReadTransformerDtoInterface;

class MessageReadTransformerDto implements MessageReadTransformerDtoInterface
{

    public function transform(MessageRead $message): MessageReadResponseDto
    {
        return new MessageReadResponseDto(
            $message->getMessageId()->getValue(),
            $message->getReaderId()->getValue(),
            $message->getReadAt()->getTimestamp()
        );
    }
}
