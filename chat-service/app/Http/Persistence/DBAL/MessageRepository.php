<?php
declare(strict_types=1);

namespace App\Http\Persistence\DBAL;

use App\Http\Mapper\MessageMapper;
use App\Message\Domain\Message;
use App\Message\Domain\Repository\MessageRepositoryInterface;
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

    public function findByUserId(): void
    {
        // TODO: Implement findByUserId() method.
    }

    protected function getMapper(): MessageMapper
    {
        return new MessageMapper();

    }
}
