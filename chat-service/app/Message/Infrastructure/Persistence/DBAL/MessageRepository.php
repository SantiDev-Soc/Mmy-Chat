<?php
declare(strict_types=1);

namespace App\Message\Infrastructure\Persistence\DBAL;

use App\Message\Domain\Message;
use App\Message\Domain\MessageRead;
use App\Message\Domain\Repository\MessageRepositoryInterface;
use App\Message\Infrastructure\Mapper\MessageMapper;
use App\Message\Infrastructure\Mapper\MessageReadMapper;
use App\Shared\Domain\ValueObject\MessageId;
use App\Shared\Domain\ValueObject\UserId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Throwable;

final readonly class MessageRepository implements MessageRepositoryInterface
{
    public function __construct(
        private Connection $connection
    )
    {
    }

    protected function getMapper(): MessageMapper
    {
        return new MessageMapper();

    }

    protected function getMessageReadMapper(): MessageReadMapper
    {
        return new MessageReadMapper();
    }


    /** @throws Exception */
    public function insert(Message $message): void
    {
        $data = $this->getMapper()->serialize($message);
        $this->connection->insert(Message::TABLE_NAME, $data);
    }

    /** @throws Exception */
    public function findByUserId(UserId $userId): ?Message
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(Message::TABLE_NAME, alias: 'm')
            ->where('m.sender_id = :userId')
            ->setParameter('userId', $userId->getValue());

        $result = $queryBuilder->executeQuery()->fetchAssociative();

        if ($result === false) {
            return null;
        }

        return $this->getMapper()->hydrate($result);
    }

    /** @throws Exception */
    public function findById(MessageId $messageId): ?Message
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(Message::TABLE_NAME, alias: 'm')
            ->where('m.id = :messageId')
            ->setParameter('messageId', $messageId->getValue());

        $result = $queryBuilder->executeQuery()->fetchAssociative();

        if ($result === false) {
            return null;
        }

        return $this->getMapper()->hydrate($result);
    }

    /** @throws Exception */
    public function getConversationsForUserId(UserId $userId): array
    {
        $qb = $this->connection->createQueryBuilder();

        $unreadSubQuery = sprintf(
            "(SELECT COUNT(*) FROM %s m2
              LEFT JOIN %s mr ON m2.id = mr.message_id AND mr.user_id = :userId
              WHERE m2.receiver_id = :userId
              AND m2.sender_id = (CASE WHEN m.sender_id = :userId THEN m.receiver_id ELSE m.sender_id END)
              AND m2.deleted_by_receiver = 'false'
              AND mr.read_at IS NULL)",
            Message::TABLE_NAME,
            MessageRead::TABLE_NAME
        );

        $qb->select('DISTINCT CASE
             WHEN m.sender_id = :userId THEN m.receiver_id ELSE m.sender_id END AS contact_id')
            ->from(Message::TABLE_NAME, 'm')
            ->addSelect("$unreadSubQuery AS unread_count")
            ->where('m.sender_id = :userId OR m.receiver_id = :userId')
            ->setParameter('userId', $userId->getValue());

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /** @throws Exception */
    public function findMessagesWithContact(UserId $userId, UserId $contactId): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('
                m.id,
                m.sender_id,
                m.receiver_id,
                m.content,
                m.sent_at,
                m.created_at,
                mr.read_at'
        )
            ->from(Message::TABLE_NAME, 'm')
            ->leftJoin('m', MessageRead::TABLE_NAME, 'mr', 'm.id = mr.message_id AND mr.user_id != m.sender_id')
            ->where('(m.sender_id = :userId AND m.receiver_id = :contactId) AND m.deleted_by_sender = false')
            ->orWhere('(m.sender_id = :contactId AND m.receiver_id = :userId) AND m.deleted_by_receiver = false')
            ->setParameter('userId', $userId->getValue())
            ->setParameter('contactId', $contactId->getValue())
            ->orderBy('m.sent_at', 'ASC');

        $messages = $qb->executeQuery()->fetchAllAssociative();

        $data = [];
        foreach ($messages as $message) {
            $data[] = $this->getMapper()->hydrate($message);
        }

        return $data;
    }

    /** @throws Exception */
    public function insertMarkAsRead(MessageRead $messageRead): void
    {
        $data = $this->getMessageReadMapper()->serialize($messageRead);

        try {
            $this->connection->insert(MessageRead::TABLE_NAME, $data);
        } catch (UniqueConstraintViolationException $e) {
            return;
        }
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function clearChatHistory(UserId $userId, UserId $contactId): void
    {
        $this->connection->transactional(function ($conn) use ($userId, $contactId) {
            $usrId = $userId->getValue();
            $cntId = $contactId->getValue();

            $conn->createQueryBuilder()
                ->update(Message::TABLE_NAME)
                ->set('deleted_by_sender', 'true')
                ->where('sender_id = :userId AND receiver_id = :contactId')
                ->setParameter('userId', $usrId)
                ->setParameter('contactId', $cntId)
                ->executeStatement();

            $conn->createQueryBuilder()
                ->update(Message::TABLE_NAME)
                ->set('deleted_by_receiver', 'true')
                ->where('receiver_id = :userId AND sender_id = :contactId')
                ->setParameter('userId', $usrId)
                ->setParameter('contactId', $cntId)
                ->executeStatement();
        });
    }


}
