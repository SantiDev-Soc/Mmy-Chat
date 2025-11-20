<?php
declare(strict_types=1);

namespace App\Shared\Domain\Event;

use App\Shared\Domain\Event\EventBusInterface;
use Illuminate\Support\Facades\Event;


final class LaravelEventBus implements EventBusInterface
{

    public function dispatch(Object $event): void
    {
        Event::dispatch($event);
    }
}
