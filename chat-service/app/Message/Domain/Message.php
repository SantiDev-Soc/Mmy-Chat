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

    public function __construct(
        MessageId $id,
        UserId $senderId,
        UserId $receiverId,
        string $content,
        DateTimeImmutable $sentAt = null
    )
    {
        $this->id = $id;
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->content = $content;
        $this->sentAt = $sentAt ?? new DateTimeImmutable();
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

    public function getSentAt(): ?DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id->getValue(),
            'sender_id' => $this->senderId->getValue(),
            'receiver_id' => $this->receiverId->getValue(),
            'content' => $this->content,
            'sent_at' => $this->sentAt?->format('Y-m-d H:i:s'),
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
        return new self($id, $senderId, $receiverId, $content, $sentAt);
    }
}
