<?php
declare(strict_types=1);

namespace App\Message\Application\Query\GetMessagesWithContact;

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Domain\ValueObject\UserId;

class GetMessagesWithContactQuery implements CommandInterface
{
    public function __construct(
        public UserId $userId,
        public UserId $contactId
    )
    {
    }

}
