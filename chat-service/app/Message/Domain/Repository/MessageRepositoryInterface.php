<?php
declare(strict_types=1);

namespace App\Message\Domain\Repository;

use App\Message\Domain\Message;
use App\Message\Domain\MessageRead;
use App\Message\Domain\ValueObject\MessageId;
use App\Message\Domain\ValueObject\UserId;

interface MessageRepositoryInterface
{
    public function insert(Message $message):void;

    /**
     * @param UserId $userId
     */
    public function findByUserId(UserId $userId): ?Message;

    public function findById(MessageId $messageId):?Message;

    public function getConversationsForUserId(UserId $userId): array;

    public function insertMarkAsRead(MessageRead $messageRead): void;

    public function findMessagesWithContact(UserId $userId, UserId $contactId): array;

    public function clearChatHistory(UserId $userId, UserId $contactId): void;
}
