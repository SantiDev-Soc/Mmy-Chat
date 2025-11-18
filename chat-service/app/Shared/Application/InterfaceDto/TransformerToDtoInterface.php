<?php
declare(strict_types=1);

namespace App\Shared\Application\InterfaceDto;

interface TransformerToDtoInterface
{
    public static function transform(array $row): array;

}
