<?php
declare(strict_types=1);

namespace App\Shared\Domain\Event;

use App\Message\Domain\Message;
use Illuminate\Support\Facades\Event;

final class LaravelEventBus implements EventBusInterface
{

    public function dispatch(Message $message): void
    {
        Event::dispatch($message);
    }
}
