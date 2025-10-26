<?php
declare(strict_types=1);

namespace App\Shared\Domain\Exception;

class InvalidUserException extends \DomainException
{
    public function __construct(string $message = 'The sender and receiver can not be the same')
    {
        parent::__construct($message);
    }
}
