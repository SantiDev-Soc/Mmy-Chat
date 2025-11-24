<?php
declare(strict_types=1);

namespace App\Message\Application\Command\MessageRead;

use App\Message\Application\DTO\MessageReadResponseDto;
use App\Message\Domain\Events\MessageReadEvent;
use App\Message\Domain\Exception\MessageNotFoundException;
use App\Message\Domain\MessageRead;
use App\Message\Domain\Repository\MessageRepositoryInterface;
use App\Shared\Application\InterfaceDto\MessageReadTransformerDtoInterface;
use App\Shared\Domain\Event\EventBusInterface;

final readonly class MessageReadHandler
{
    public function __construct(
        private MessageRepositoryInterface $messageRepository,
        private MessageReadTransformerDtoInterface $messageReadTransformerDto,
        private EventBusInterface $eventBus,
    )
    {
    }

    public function __invoke(MessageReadCommand $command): MessageReadResponseDto
    {
        $originalMessage = $this->messageRepository->findById($command->messageId);

        if (null === $originalMessage) {
            throw new MessageNotFoundException();
        }

        $participants = [
            $originalMessage->getSenderId()->getValue(),
            $originalMessage->getReceiverId()->getValue()
        ];

        $markAsRead = new MessageRead(
            $command->messageId,
            $command->readerId,
            $command->readAt
        );

        $this->messageRepository->insertMarkAsRead($markAsRead);

        sort($participants);
        $channel = $participants[0] . '.' . $participants[1];

        $event = new MessageReadEvent(
            $channel,
            $command->readerId->getValue(),
            [
                $command->messageId->getValue()
            ]
        );

        $this->eventBus->dispatch($event);

        return $this->messageReadTransformerDto->transform($markAsRead);
    }
}
