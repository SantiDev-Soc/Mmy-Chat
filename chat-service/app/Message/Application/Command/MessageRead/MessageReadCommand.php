<?php
declare(strict_types=1);

namespace App\Message\Application\Command\MessageRead;

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Domain\ValueObject\MessageId;
use App\Shared\Domain\ValueObject\UserId;
use DateTimeImmutable;

class MessageReadCommand implements CommandInterface
{
    public function __construct(
        public MessageId $messageId,
        public UserId $readerId,
        public DateTimeImmutable $readAt
    )
    {
    }

}
