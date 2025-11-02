<?php
declare(strict_types=1);

namespace App\Message\Application\Conversations;

use App\Message\Domain\Repository\MessageRepositoryInterface;
use App\Shared\Domain\Event\EventBusInterface;
use App\Shared\Domain\Exception\InvalidUserException;
use App\Shared\Domain\Exception\MessageNotFoundException;

class GetConversationsHandler
{
    public function __construct(
        private MessageRepositoryInterface $messageRepository,
        private EventBusInterface $eventBus,
    )
    {
    }

    public function __invoke(GetConversationsCommand $command): array
    {
        $conversations = $this->messageRepository->findByUserId($command->userId);
        if(null === $conversations) {
            throw new MessageNotFoundException();
        }


        return [];
    }
}
