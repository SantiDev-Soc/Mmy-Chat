<?php
declare(strict_types=1);

namespace App\Http\Persistence\DBAL;

use App\Http\Mapper\MessageMapper;
use App\Message\Domain\Message;
use App\Message\Domain\Repository\MessageRepositoryInterface;
use App\Shared\Domain\ValueObject\MessageId;
use App\Shared\Domain\ValueObject\UserId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

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


    /** @throws Exception */
    public function insert(Message $message): void
    {
        $data = $this->getMapper()->serialize($message);
        $this->connection->insert(Message::TABLE_NAME, $data);
    }

    public function readBy(): void
    {
        // TODO: Implement readBy() method.
    }

    /** @throws Exception */
    public function findByUserId(UserId $userId): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(Message::TABLE_NAME, alias: 'm')
            ->where('m.sender_id = :userId')
            ->setParameter('userId', $userId->getValue());

        $results = $queryBuilder->executeQuery()->fetchAllAssociative();

        $messages = [];
        foreach ($results as $message) {
            $messages[] = $this->getMapper()->hydrate($message);
        }

        return $messages;
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

        return $this->getMapper()->hydrate($result);
    }

    /** @throws Exception */
    public function getConversationsForUserId(UserId $userId): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->select('DISTINCT CASE
                    WHEN m.sender_id = :userId THEN m.receiver_id
                    ELSE m.sender_id
                  END AS contact_id')
            ->from(Message::TABLE_NAME, 'm')
            ->where('m.sender_id = :userId OR m.receiver_id = :userId')
            ->setParameter('userId', $userId->getValue());

        $results = $queryBuilder->executeQuery()->fetchFirstColumn();

        return array_map(static fn($id) => UserId::create((string)$id), $results);
    }

    /** @throws Exception */
    public function findMessagesWithContact(UserId $userId, UserId $contactId): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('m.id, m.sender_id, m.receiver_id, m.content, m.created_at')
            ->from(Message::TABLE_NAME, 'm')
            ->where('(m.sender_id = :userId AND m.receiver_id = :contactId)')
            ->orWhere('(m.sender_id = :contactId AND m.receiver_id = :userId)')
            ->setParameter('userId', $userId->getValue())
            ->setParameter('contactId', $contactId->getValue())
            ->orderBy('m.created_at', 'ASC');

        $messages = $qb->executeQuery()->fetchAllAssociative();

        $data = [];
        foreach ($messages as $message) {
            $data[] = $this->getMapper()->hydrate($message);
        }

        return $data;
    }


}
