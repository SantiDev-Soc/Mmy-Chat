<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Message\Application\CreateMessage\CreateMessageCommand;
use App\Message\Application\CreateMessage\CreateMessageHandler;
use App\Shared\Domain\ValueObject\UserId;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

class MessageController extends Controller
{
    public function __construct(
        private readonly CreateMessageHandler $handler
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
}
