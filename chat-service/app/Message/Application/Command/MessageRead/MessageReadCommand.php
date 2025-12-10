<?php
declare(strict_types=1);

namespace App\Message\Application\Command\MessageRead;

use App\Message\Domain\ValueObject\MessageId;
use App\Message\Domain\ValueObject\UserId;
use App\Shared\Application\Command\CommandInterface;
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
