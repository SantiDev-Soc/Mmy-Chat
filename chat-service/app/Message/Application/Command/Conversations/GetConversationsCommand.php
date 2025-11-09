<?php
declare(strict_types=1);

namespace App\Message\Application\Command\Conversations;

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Domain\ValueObject\UserId;

class GetConversationsCommand implements CommandInterface
{
    public function __construct(public UserId $userId)
    {
    }

}
