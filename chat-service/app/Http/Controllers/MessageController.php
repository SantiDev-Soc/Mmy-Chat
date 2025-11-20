<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Message\Application\Command\CreateMessage\CreateMessageCommand;
use App\Message\Application\Command\CreateMessage\CreateMessageHandler;
use App\Message\Application\Query\GetConversations\GetConversationsQuery;
use App\Message\Application\Query\GetConversations\GetConversationsHandler;
use App\Message\Application\Query\GetMessagesWithContact\GetMessagesWithContactHandler;
use App\Message\Application\Query\GetMessagesWithContact\GetMessagesWithContactQuery;
use App\Shared\Domain\ValueObject\UserId;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

final class MessageController extends Controller
{
    public function __construct(
        private readonly CreateMessageHandler $handler,
        private readonly GetConversationsHandler $getConversationsHandler,
        private readonly GetMessagesWithContactHandler $getMessagesWithContactHandler,
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

            $message = ($this->handler)($command);

            return response()->json([
                'success' => true,
                'message' => $message->serialize()
            ], 201);

        }catch (Throwable $exception){
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

            $conversations = ($this->getConversationsHandler)($command);

            return response()->json([
                'success' => true,
                'message' => $conversations
            ], 201);

        } catch (Throwable $exception){
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

            $messagesWithContact = ($this->getMessagesWithContactHandler)($command);

            return response()->json([
                'success' => true,
                'message' => $messagesWithContact
            ], 201);

        } catch (Throwable $exception){
            return response()->json([
                'success' => false,
                'error' => $exception->getMessage()
            ], 500);
        }
    }

}
