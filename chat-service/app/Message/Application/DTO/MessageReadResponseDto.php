<?php
declare(strict_types=1);

namespace App\Message\Application\DTO;

class MessageReadResponseDto
{
    public function  __construct(
        public string $messageId,
        public string $readerId,
        public int $readAt
    )
    {
    }

}
