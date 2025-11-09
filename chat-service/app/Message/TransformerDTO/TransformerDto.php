<?php
declare(strict_types=1);

namespace App\Message\TransformerDTO;

use App\Message\Application\DTO\ConversationDTO;
use App\Shared\Application\InterfaceDto\TransformerToDtoInterface;

class TransformerDto implements TransformerToDtoInterface
{
    public static function transform(array $row): array
    {
        $data = array();

        foreach ($row as $message) {
            $dto = new ConversationDTO();
            $dto->id = $message->getId();
            $dto->senderId = $message->getSenderId();
            $dto->receiverId = $message->getReceiverId();
            $dto->content = $message->getContent();

            $data[] = $dto;
        }

        return $data;
    }
}
