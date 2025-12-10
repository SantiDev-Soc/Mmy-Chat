<?php
declare(strict_types=1);

namespace App\Message\Domain;

use App\Message\Domain\ValueObject\MessageId;
use App\Message\Domain\ValueObject\UserId;
use DateTimeImmutable;

class MessageRead
{
    public const TABLE_NAME = 'message_reads';
    private MessageId $messageId;
    private UserId $readerId;
    private DateTimeImmutable $readAt;

    public function __construct(
        MessageId $messageId,
        UserId $readerId,
        DateTimeImmutable $readAt
    )
    {
        $this->messageId = $messageId;
        $this->readerId = $readerId;
        $this->readAt = $readAt ?? new DateTimeImmutable();
    }

    public function markAsRead(): void
    {
        $this->readAt = new DateTimeImmutable();
    }

    public static function deserialize(array $row): self
    {
        $messageId = MessageId::create($row['message_id']);
        $readerId = UserId::create($row['user_id']);
        if (isset($row['read_at']) && is_string($row['read_at'])) {
            $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $row['read_at']);
            $readAt = $date !== false ? $date : new DateTimeImmutable();
        } else {
            $readAt = new DateTimeImmutable();
        }

        return new self($messageId, $readerId, $readAt);
    }

    public function serialize(): array
    {
        return [
            'message_id' => $this->messageId->getValue(),
            'user_id' => $this->readerId->getValue(),
            'read_at' => $this->readAt->format('Y-m-d H:i:s'),
        ];
    }

    public function getReadAt(): DateTimeImmutable
    {
        return $this->readAt;
    }

    public function getReaderId(): UserId
    {
        return $this->readerId;
    }

    public function getMessageId(): MessageId
    {
        return $this->messageId;
    }


}
