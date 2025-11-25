<?php
declare(strict_types=1);

namespace App\Message\Application\Command\DeleteChat;

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Domain\ValueObject\UserId;

class DeleteChatCommand implements CommandInterface
{
    public function __construct(
        public UserId $userId,
        public UserId $contactId
    ) {
    }
}
