<?php
declare(strict_types=1);

namespace App\Message\Domain;

use App\Shared\Domain\ValueObject\MessageId;
use App\Shared\Domain\ValueObject\UserId;
use DateTimeImmutable;

class MessageRead
{
    public const TABLE_NAME = 'message_reads';
    private MessageId $messageId;
    private UserId $userId;
    private DateTimeImmutable $readAt;

    public function __construct(
        MessageId $messageId,
        UserId $userId,
        DateTimeImmutable $readAt = null
    )
    {
        $this->messageId = $messageId;
        $this->userId = $userId;
        $this->readAt = $readAt ?? new DateTimeImmutable();
    }

    public function markAsRead(): void {
        $this->readAt = new DateTimeImmutable();
    }

    public static function deserialize(array $row): self
    {
        $messageId = $row['message_id'];
        $userId = $row['user_id'];
        $readAt = isset($row['read_at'])
            ? DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $row['read_at']) :
            new DateTimeImmutable();
        return new self($messageId, $userId, $readAt);
    }

    public function serialize(): array
    {
        return [
            'messageId' => $this->messageId->getValue(),
            'userId' => $this->userId->getValue(),
            'readAt' => $this->readAt->format('Y-m-d H:i:s'),
        ];
    }

    public function getReadAt(): DateTimeImmutable
    {
        return $this->readAt;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getMessageId(): MessageId
    {
        return $this->messageId;
    }


}
