<?php
declare(strict_types=1);

namespace App\Message\Domain\Exception;

class MessageNotFoundException extends \DomainException
{
    public function __construct(string $message = 'The current user has no message')
    {
        parent::__construct($message);
    }
}
