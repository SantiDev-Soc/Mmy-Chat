<?php
declare(strict_types=1);

namespace App\Message\Application\Query\GetMessagesWithContact;

use App\Message\Domain\Exception\MessageNotFoundException;
use App\Message\Domain\Repository\MessageRepositoryInterface;
use App\Shared\Application\InterfaceDto\TransformerToDtoInterface;

final readonly class GetMessagesWithContactHandler
{
    public function __construct(
        private MessageRepositoryInterface $messageRepository,
        private TransformerToDtoInterface $transformerToDto
    )
    {
    }

    public function __invoke(GetMessagesWithContactQuery $query): array
    {
        $message = $this->messageRepository->findByUserId($query->userId);
        if (null === $message) {
            throw new MessageNotFoundException();
        }

        $messages = $this->messageRepository->findMessagesWithContact(
            $query->userId,
            $query->contactId
        );

        $data = [];
        foreach ($messages as $message) {
            $data[] = $this->transformerToDto->transform($message);
        }

        return $data;
    }
}

