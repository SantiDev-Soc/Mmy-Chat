<?php
declare(strict_types=1);

namespace App\Message\Domain;

use App\Shared\Domain\ValueObject\MessageId;
use App\Shared\Domain\ValueObject\UserId;
use DateTimeImmutable;

class Message
{
    public const TABLE_NAME = 'messages';
    private MessageId $id;
    private UserId $senderId;
    private UserId $receiverId;
    private string $content;
    private ?DateTimeImmutable $sentAt;
    private ?DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt;


    public function __construct(
        MessageId $id,
        UserId $senderId,
        UserId $receiverId,
        string $content,
        DateTimeImmutable $sentAt = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null
    )
    {
        $this->id = $id;
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->content = $content;
        $this->sentAt = $sentAt ?? new DateTimeImmutable();
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
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

    public function getSentAt(): ? int
    {
        return $this->sentAt->getTimestamp();
    }

    public function getUpdatedAt(): ? int
    {
        return $this->updatedAt->getTimestamp();
    }

    public function getCreatedAt(): ? int
    {
        return $this->createdAt->getTimestamp();
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id->getValue(),
            'sender_id' => $this->senderId->getValue(),
            'receiver_id' => $this->receiverId->getValue(),
            'content' => $this->content,
            'sent_at' => $this->sentAt?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }

    public static function deserialize(array $data): self
    {
        $id = MessageId::create((string)$data['id']);
        $senderId = UserId::create((string)$data['sender_id']);
        $receiverId = UserId::create((string)$data['receiver_id']);
        $content = $data['content'];
        $sentAt = isset($data['sent_at'])
            ? DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['sent_at']) :
            new DateTimeImmutable();
        $createdAt = isset($data['created_at']) ? DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['created_at']) :
            new DateTimeImmutable();
        $updatedAt = isset($data['updated_at']) ? DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['updated_at']) :
            new DateTimeImmutable();

        return new self($id, $senderId, $receiverId, $content, $sentAt, $createdAt, $updatedAt);
    }
}
