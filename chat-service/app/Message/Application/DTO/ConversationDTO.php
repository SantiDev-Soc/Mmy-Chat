<?php
declare(strict_types=1);

namespace App\Message\Application\DTO;

class ConversationDTO
{
    public string $id;
    public string $senderId;
    public string $receiverId;

    public string $content;
}
