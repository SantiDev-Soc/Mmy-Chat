<?php
declare(strict_types=1);

namespace App\Message\Application\Command\DeleteChat;

use App\Http\Persistence\DBAL\MessageRepository;
use App\Shared\Domain\Event\EventBusInterface;
use Doctrine\DBAL\Exception;

final readonly class DeleteChatHandler
{
    public function __construct(
        private MessageRepository $messageRepository,
        private EventBusInterface $eventBus,
    )
    {
    }

    /** @throws Exception */
    public function __invoke(DeleteChatCommand $command): void
    {
        $this->messageRepository->deleteChatHistory($command->userId, $command->contactId);
    }
}
