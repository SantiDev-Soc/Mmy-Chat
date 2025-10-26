<?php
declare(strict_types=1);

namespace App\Shared\Domain\Aggregate;

abstract class AggregateRootId implements \JsonSerializable
{

    protected int|string $id;

    public function __construct(int|string $id)
    {
        $this->id = $id;
    }

    public function getValue(): int|string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function jsonSerialize(): string
    {
        return (string) $this->getValue();
    }

    public function equals(self $other): bool
    {
        return $this->id === $other->id;
    }

}
