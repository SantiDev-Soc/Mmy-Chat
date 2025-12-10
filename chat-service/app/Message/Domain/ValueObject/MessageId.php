<?php
declare(strict_types=1);

namespace App\Message\Domain\ValueObject;

use App\Shared\Domain\Aggregate\AggregateRootId;
use Symfony\Component\Uid\Uuid;

class MessageId extends AggregateRootId
{
    public static function create(string $value): self
    {
        return new self($value);
    }

    public static function random(): self
    {
        return new self((string)Uuid::v4());
    }
}
