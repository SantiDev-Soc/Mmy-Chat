<?php
declare(strict_types=1);

namespace App\Message\Application\Query\GetConversations;

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Domain\ValueObject\UserId;

class GetConversationsQuery implements CommandInterface
{
    public function __construct(public UserId $userId)
    {
    }

}
