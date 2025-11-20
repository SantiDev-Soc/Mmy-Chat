<?php
declare(strict_types=1);

namespace App\Message\TransformerDTO;

use App\Message\Application\DTO\MessageResponseDto;
use App\Message\Domain\Message;
use App\Shared\Application\InterfaceDto\TransformerToDtoInterface;

class TransformerDto implements TransformerToDtoInterface
{
    public static function transform(Message $message): MessageResponseDto
    {
            $dto = new MessageResponseDto();
            $dto->id = $message->getId()->getValue();
            $dto->senderId = $message->getSenderId()->getValue();
            $dto->receiverId = $message->getReceiverId()->getValue();
            $dto->content = $message->getContent();
            $dto->createdAt = $message->getCreatedAt();

        return $dto;
    }

}
