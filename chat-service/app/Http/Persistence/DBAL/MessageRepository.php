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
            ->setParameter('userId', $userId);

        $results = $queryBuilder->executeQuery()->fetchAllAssociative();

        $messages = [];
        foreach ($results as $message) {
            $messages[] = $this->getMapper()->hydrate($message);
        }

        return $messages;
    }

    protected function getMapper(): MessageMapper
    {
        return new MessageMapper();

    }

    /** @throws Exception */
    public function findById(MessageId $messageId): ?Message
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(Message::TABLE_NAME, alias: 'm')
            ->where('m.id = :messageId')
            ->setParameter('messageId', $messageId);

        $result = $queryBuilder->executeQuery()->fetchAssociative();

        return $this->getMapper()->hydrate($result);
    }
}
