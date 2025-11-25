<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Message\Application\Command\CreateMessage\CreateMessageCommand;
use App\Message\Application\Command\CreateMessage\CreateMessageHandler;
use App\Message\Application\Command\DeleteChat\DeleteChatCommand;
use App\Message\Application\Command\DeleteChat\DeleteChatHandler;
use App\Message\Application\Command\MessageRead\MessageReadCommand;
use App\Message\Application\Command\MessageRead\MessageReadHandler;
use App\Message\Application\Query\GetConversations\GetConversationsQuery;
use App\Message\Application\Query\GetConversations\GetConversationsHandler;
use App\Message\Application\Query\GetMessagesWithContact\GetMessagesWithContactHandler;
use App\Message\Application\Query\GetMessagesWithContact\GetMessagesWithContactQuery;
use App\Message\Domain\Exception\MessageNotFoundException;
use App\Shared\Domain\Exception\InvalidUserException;
use App\Shared\Domain\ValueObject\MessageId;
use App\Shared\Domain\ValueObject\UserId;
use DateTimeImmutable;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

final class MessageController extends Controller
{

    public function __construct(
        private readonly CreateMessageHandler $handler,
        private readonly GetConversationsHandler $getConversationsHandler,
        private readonly GetMessagesWithContactHandler $getMessagesWithContactHandler,
        private readonly MessageReadHandler $messageReadHandler,
        private readonly DeleteChatHandler $deleteChatHandler,
    )
    {
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->request->all();

        try {
            $command = new CreateMessageCommand(
                UserId::create($data['sender_id']),
                UserId::create($data['receiver_id']),
                $data['content']);

            $messageDto = ($this->handler)($command);

            return response()->json([
                'success' => true,
                'message' => $messageDto
            ], 201);

        } catch (InvalidUserException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 401);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        } catch (Throwable $exception) {
            return response()->json([
                'success' => false,
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    public function getConversations(string $userId): JsonResponse
    {
        try {
            $command = new GetConversationsQuery(UserId::create($userId));

            $conversationsInDto = ($this->getConversationsHandler)($command);

            return response()->json([
                'success' => true,
                'message' => $conversationsInDto
            ], 200);

        } catch (MessageNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 404);
        } catch (Throwable $exception) {
            return response()->json([
                'success' => false,
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    public function getMessagesWithContact(string $contactId, Request $request): JsonResponse
    {
        $userId = $request->query('user_id');

        try {
            $command = new GetMessagesWithContactQuery(
                UserId::create($userId),
                UserId::create($contactId),
            );

            $messagesWithContactInDto = ($this->getMessagesWithContactHandler)($command);

            return response()->json([
                'success' => true,
                'message' => $messagesWithContactInDto
            ], 201);

        } catch (Throwable $exception) {
            return response()->json([
                'success' => false,
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    public function messagesRead(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message_id' => 'required|string',
            'reader_id' => 'required|string',
        ]);

        $readAt = new DateTimeImmutable();

        try {
            $command = new MessageReadCommand(
                MessageId::create($data['message_id']),
                UserId::create($data['reader_id']),
                $readAt,
            );

            $messageReadResponseDto = ($this->messageReadHandler)($command);

            return response()->json([
                'success' => true,
                'message' => $messageReadResponseDto
            ], 201);

        } catch (Throwable $exception) {
            return response()->json([
                'success' => false,
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    public function deleteConversation(string $contactId, Request $request): JsonResponse
    {
        $userId = $request->query('user_id');

        try {
            $command = new DeleteChatCommand(
                UserId::create($userId),
                UserId::create($contactId)
            );

            ($this->deleteChatHandler)($command);

            return response()->json(['success' => true], 200);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
