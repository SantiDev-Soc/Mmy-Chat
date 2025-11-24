<?php
declare(strict_types=1);

namespace App\Message\Application\DTO;

class MessageResponseDto
{
    public function __construct(
        public string $id,
        public string $content,
        public string $sender_id,
        public string $receiver_id,
        public int $sent_at,
        public ?int $created_at,
        public ?int $updated_at,
    )
    {
    }
}
