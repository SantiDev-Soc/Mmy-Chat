<?php
declare(strict_types=1);

namespace App\Shared\Application\InterfaceDto;

use App\Message\Application\DTO\MessageResponseDto;
use App\Message\Domain\Message;

interface TransformerToDtoInterface
{
    public static function transform(Message $message): MessageResponseDto;

}
