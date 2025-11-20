<?php
declare(strict_types=1);

namespace App\Message\Application\Query\GetConversations;

use App\Message\Domain\Repository\MessageRepositoryInterface;
use App\Shared\Application\InterfaceDto\TransformerToDtoInterface;
use function PHPUnit\Framework\isEmpty;

final readonly class GetConversationsHandler
{
    public function __construct(
        private MessageRepositoryInterface $messageRepository,
        private TransformerToDtoInterface  $transformerToDto
    )
    {
    }

    public function __invoke(GetConversationsQuery $command): array
    {
        $conversations = $this->messageRepository->getConversationsForUserId($command->userId);
        if(isEmpty($conversations)) {
            return [];
        }

        $messages = [];
        foreach ($conversations as $conversation) {
            $messages[] = $this->transformerToDto::transform($conversation);
        }

        return $messages;
    }
}
