<?php
declare(strict_types=1);

namespace App\Message\Application\Query\GetConversations;

use App\Message\Domain\Repository\MessageRepositoryInterface;
use App\Shared\Application\InterfaceDto\ConversationTransformerDtoInterface;

final readonly class GetConversationsHandler
{
    public function __construct(
        private MessageRepositoryInterface $messageRepository,
        private ConversationTransformerDtoInterface  $conversationTransformerDto
    )
    {
    }

    public function __invoke(GetConversationsQuery $command): array
    {
        $conversations = $this->messageRepository->getConversationsForUserId($command->userId);
        if(empty($conversations)) {
            return [];
        }

        $messages = [];
        foreach ($conversations as $conversation) {
            $messages[] = $this->conversationTransformerDto->transform($conversation);
        }

        return $messages;
    }
}
