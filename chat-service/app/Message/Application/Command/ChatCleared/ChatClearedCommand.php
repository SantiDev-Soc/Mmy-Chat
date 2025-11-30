<?php
declare(strict_types=1);

namespace App\Message\Application\Command\ChatCleared;

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Domain\ValueObject\UserId;

class ChatClearedCommand implements CommandInterface
{
    public function __construct(
        public UserId $userId,
        public UserId $contactId
    ) {
    }
}
