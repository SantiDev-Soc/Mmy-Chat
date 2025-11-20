<?php
declare(strict_types=1);

namespace App\Message\Application\DTO;

class MessageResponseDto
{
    public string $id;
    public string $senderId;
    public string $receiverId;
    public string $content;
    public int $createdAt;
}
