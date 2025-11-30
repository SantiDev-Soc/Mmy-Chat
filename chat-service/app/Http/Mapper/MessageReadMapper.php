<?php
declare(strict_types=1);

namespace App\Http\Mapper;

use App\Message\Domain\MessageRead;
use App\Shared\Domain\Repository\MapperInterface;

class MessageReadMapper implements MapperInterface
{
    public function hydrate(array $row, ?array $additionalInfo = []): MessageRead
    {
        return MessageRead::deserialize($row);
    }

    /**
     * @param MessageRead $entity
     */
    public function serialize($entity): array
    {
        return $entity->serialize();
    }
}
