<?php
declare(strict_types=1);

namespace App\Message\Application\DTO;

class ConversationDto
{
    public function __construct(
        public string $contact_id,
        public int $unread_count
    )
    {
    }
}
