<?php
declare(strict_types=1);

namespace App\Message\Domain;

use App\Message\Domain\ValueObject\MessageId;
use App\Message\Domain\ValueObject\UserId;
use DateTimeImmutable;
use Exception;

class Message
{
    public const TABLE_NAME = 'messages';
    private MessageId $id;
    private UserId $senderId;
    private UserId $receiverId;
    private string $content;
    private DateTimeImmutable $sentAt;
    private ?DateTimeImmutable $readAt;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;
    private bool $deletedBySender;
    private bool $deletedByReceiver;

    public function __construct(
        MessageId $id,
        UserId $senderId,
        UserId $receiverId,
        string $content,
        DateTimeImmutable $sentAt,
        ?DateTimeImmutable $readAt,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
        bool $deletedBySender = false,
        bool $deletedByReceiver = false
    )
    {
        $this->id = $id;
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->content = $content;
        $this->sentAt = $sentAt ?? new DateTimeImmutable();
        $this->readAt = $readAt;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
        $this->deletedBySender = $deletedBySender;
        $this->deletedByReceiver = $deletedByReceiver;
    }

    public function getId(): MessageId
    {
        return $this->id;
    }

    public function getSenderId(): UserId
    {
        return $this->senderId;
    }

    public function getReceiverId(): UserId
    {
        return $this->receiverId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getSentAt(): DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function getReadAt(): ?DateTimeImmutable
    {
        return $this->readAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id->getValue(),
            'sender_id' => $this->senderId->getValue(),
            'receiver_id' => $this->receiverId->getValue(),
            'content' => $this->content,
            'sent_at' => $this->sentAt->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
            'deleted_by_sender' => $this->deletedBySender ? 1 : 0,
            'deleted_by_receiver' => $this->deletedByReceiver ? 1 : 0,
        ];
    }

    public static function deserialize(array $data): self
    {
        $id = MessageId::create((string)$data['id']);
        $senderId = UserId::create((string)$data['sender_id']);
        $receiverId = UserId::create((string)$data['receiver_id']);
        $content = $data['content'];
        $sentAtRaw = $data['sent_at'] ?? $data['created_at'] ?? null;
        $sentAt = self::dateTime($sentAtRaw);
        $readAtRaw = $data['read_at'] ?? null;
        $readAt = $readAtRaw ? self::dateTime($readAtRaw) : null;
        $createdAt = isset($data['created_at']) ? self::dateTime($data['created_at']) : $sentAt;
        $updatedAt = isset($data['updated_at']) ? self::dateTime($data['updated_at']) : $sentAt;
        $delSender = isset($data['deleted_by_sender']) && (bool)$data['deleted_by_sender'];
        $delReceiver = isset($data['deleted_by_receiver']) && (bool)$data['deleted_by_receiver'];

        return new self(
            $id,
            $senderId,
            $receiverId,
            $content,
            $sentAt,
            $readAt,
            $createdAt,
            $updatedAt,
            $delSender,
            $delReceiver
        );
    }

    private static function dateTime(?string $dateStr): DateTimeImmutable
    {
        if (empty($dateStr)) {
            return new DateTimeImmutable();
        }
        try {
            return new DateTimeImmutable($dateStr);
        } catch (Exception $e) {
            return new DateTimeImmutable();
        }
    }

}
