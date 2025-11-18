<?php
declare(strict_types=1);

namespace App\Message\TransformerDTO;

use App\Message\Application\DTO\MessageDto;
use App\Shared\Application\InterfaceDto\TransformerToDtoInterface;

class TransformerDto implements TransformerToDtoInterface
{
    public static function transform(array $row): array
    {
        $data = [];

        foreach ($row as $message) {
            $dto = new MessageDto();
            $dto->id = $message->getId();
            $dto->senderId = $message->getSenderId();
            $dto->receiverId = $message->getReceiverId();
            $dto->content = $message->getContent();
            $dto->createdAt = $message->getCreatedAt();

            $data[] = $dto;
        }

        return $data;
    }

}
