<?php
declare(strict_types=1);

namespace App\Message\Application\Command\CreateMessage;

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Domain\ValueObject\UserId;

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
