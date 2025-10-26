<?php
declare(strict_types=1);

namespace App\Http\Mapper;

use App\Message\Domain\Message;
use App\Shared\Domain\Repository\MapperInterface;

class MessageMapper implements MapperInterface
{
    public function hydrate(array $row, ?array $additionalInfo = []): Message
    {
        return Message::deserialize($row);
    }

    /**
     * @param Message $entity
     */
    public function serialize($entity): array
    {
        return $entity->serialize();
    }
}
