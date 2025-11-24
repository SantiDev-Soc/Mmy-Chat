<?php
declare(strict_types=1);

namespace App\Message\Application\Command\CreateMessage;

use App\Message\Domain\Events\MessageSent;
use App\Message\Domain\Message;
use App\Message\Domain\Repository\MessageRepositoryInterface;
use App\Shared\Domain\Event\EventBusInterface;
use App\Shared\Domain\Exception\InvalidUserException;
use App\Shared\Domain\ValueObject\MessageId;
use DateTimeImmutable;
use InvalidArgumentException;

final readonly class CreateMessageHandler
{

    public function __construct(
        private MessageRepositoryInterface $messageRepository,
        private EventBusInterface $eventBus,
    )
    {
    }

    public function __invoke(CreateMessageCommand $command): Message
    {

        if ($command->senderId->equals($command->receiverId)) {
            throw new InvalidUserException();
        }

        if (trim($command->content) === '') {
            throw new InvalidArgumentException('Content cannot be empty');
        }

        $message = new Message(
            MessageId::random(),
            $command->senderId,
            $command->receiverId,
            $command->content,
            new DateTimeImmutable(),
        );

        $this->messageRepository->insert($message);

        $this->eventBus->dispatch(new MessageSent($message));

        return $message;
    }

}
