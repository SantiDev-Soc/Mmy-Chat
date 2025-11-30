<?php
declare(strict_types=1);

namespace App\Message\Application\Command\ChatCleared;

use App\Http\Persistence\DBAL\MessageRepository;
use App\Message\Domain\Events\ChatCleared;
use App\Shared\Domain\Event\EventBusInterface;
use Doctrine\DBAL\Exception;
use Throwable;

final readonly class ChatClearedHandler
{
    public function __construct(
        private MessageRepository $messageRepository,
        private EventBusInterface $eventBus,
    )
    {
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function __invoke(ChatClearedCommand $command): void
    {
        $this->messageRepository->clearChatHistory($command->userId, $command->contactId);

        $event = new ChatCleared(
            $command->userId->getValue(),
            $command->contactId->getValue()
        );

        $this->eventBus->dispatch($event);
    }
}
