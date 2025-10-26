<?php
declare(strict_types=1);

namespace App\Shared\Domain\Event;

use App\Message\Domain\Message;

interface EventBusInterface
{
    public function dispatch(Message $message): void;
}
