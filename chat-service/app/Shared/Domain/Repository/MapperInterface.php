<?php
declare(strict_types=1);

namespace App\Shared\Domain\Repository;

interface MapperInterface
{
    /* * @phpstan-ignore-next-line */
    public function hydrate(array $row, ?array $additionalInfo = []): mixed;

    /* * @phpstan-ignore-next-line */
    public function serialize($entity): array;

}
