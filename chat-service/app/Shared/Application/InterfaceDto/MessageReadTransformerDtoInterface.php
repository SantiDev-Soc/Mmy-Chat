<?php
declare(strict_types=1);

namespace App\Shared\Application\InterfaceDto;

use App\Message\Application\DTO\MessageReadResponseDto;
use App\Message\Domain\MessageRead;

interface MessageReadTransformerDtoInterface
{
    public function transform(MessageRead $message): MessageReadResponseDto;
}
