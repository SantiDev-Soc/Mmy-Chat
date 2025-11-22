<?php
declare(strict_types=1);

namespace App\Message\Domain\Repository;

use App\Message\Domain\Message;
use App\Shared\Domain\ValueObject\MessageId;
use App\Shared\Domain\ValueObject\UserId;

interface MessageRepositoryInterface
{
    public function insert(Message $message):void;

    /**
     * @param UserId $userId
     */
    public function findByUserId(UserId $userId): ?Message;

    public function findById(MessageId $messageId):?Message;

    public function getConversationsForUserId(UserId $userId): array;

    public function readBy():void;

    public function findMessagesWithContact(UserId $userId, UserId $contactId): array;

}
