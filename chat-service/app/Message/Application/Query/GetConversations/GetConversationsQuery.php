<?php
declare(strict_types=1);

namespace App\Message\Application\Query\GetConversations;

use App\Message\Domain\ValueObject\UserId;
use App\Shared\Application\Command\CommandInterface;

class GetConversationsQuery implements CommandInterface
{
    public function __construct(public UserId $userId)
    {
    }

}
