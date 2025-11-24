<?php
declare(strict_types=1);

namespace App\Message\Application\Command\MessageReaded;

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Domain\ValueObject\MessageId;
use App\Shared\Domain\ValueObject\UserId;

class MessageReadedCommand implements CommandInterface
{
    public function __construct(
        public MessageId $messageId,
        public UserId $userId,
    )
    {
    }

}
