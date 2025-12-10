<?php
declare(strict_types=1);

namespace App\Message\Application\Command\ChatCleared;

use App\Message\Domain\ValueObject\UserId;
use App\Shared\Application\Command\CommandInterface;

class ChatClearedCommand implements CommandInterface
{
    public function __construct(
        public UserId $userId,
        public UserId $contactId
    ) {
    }
}
