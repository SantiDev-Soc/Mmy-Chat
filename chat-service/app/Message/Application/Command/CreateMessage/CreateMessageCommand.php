<?php
declare(strict_types=1);

namespace App\Message\Application\Command\CreateMessage;

use App\Message\Domain\ValueObject\UserId;
use App\Shared\Application\Command\CommandInterface;

final class CreateMessageCommand implements CommandInterface
{
    public function __construct(
        public UserId $senderId,
        public UserId $receiverId,
        public string $content,
    )
    {
    }
}
